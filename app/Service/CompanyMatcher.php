<?php

namespace App\Service;

use Analog\Handler\Variable;
use Analog\Logger;
use PDO;
use Analog\Analog;
use Analog\Handler\File;

class CompanyMatcher
{
    private PDO $db;
    private array $matches = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function match(array $post): void
    {
        $s = $this->db->prepare(
            'SELECT company_id
                        FROM company_matching_settings 
                    WHERE   type = :surveyType AND
                            bedrooms LIKE :bedrooms AND
                            postcodes = :postcode
                    LIMIT 3'
        );

        $postCodePrefix = preg_split('/(?=\d)/', $post['postcode'])[0];

        $numOfBedrooms = $this->sortBedrooms($post['bedrooms']);

        $s->bindParam(':bedrooms', $numOfBedrooms);
        $s->bindParam(':surveyType', $post['surveyType']);
        $s->bindValue(':postcode', $postCodePrefix);
        $s->execute();

        $results = $s->fetchAll(PDO::FETCH_ASSOC);
        $s = null;

        $companyList = $this->findCompanies($results);

        $this->matches = $companyList;
    }

    public function findCompanies(array $results): array
    {
        $companyList = [];

        foreach ($results as $r) {
            $companyId = $r['company_id'];

            $c = $this->db->prepare(
                'SELECT   id,
                                name,
                                credits,
                                description,
                                email,
                                phone,
                                website
                        FROM companies WHERE id = :companyId'
            );
            $c->bindParam(':companyId', $companyId, PDO::PARAM_INT);

            $c->execute();

            $company = $c->fetch();

            $this->deductCredits($company);

            $companyList[] = $company;
        }

        return $companyList;
    }

    public function sortBedrooms(array $bedrooms): string
    {
        $listOfOptions = '[%';

        foreach ($bedrooms['options'] as $bedroom => $number) {
            $bedroomSelection = '"' . $number[0] . '"' . ',';
            $listOfOptions = $listOfOptions . $bedroomSelection;
        }

        $listOfOptions = rtrim($listOfOptions, ',');

        return $listOfOptions . '%]';
    }

    public function results(): array
    {
        return $this->matches;
    }

    public function deductCredits(array $company): void
    {
        if ((int) $company['credits'] > 0) {
            $newCredits = (int) $company['credits'] - 1;

            $statement = $this->db->prepare(
                'UPDATE companies SET credits = :credits WHERE id = :company_id'
            );

            $statement->bindParam(':credits', $newCredits, PDO::PARAM_INT);
            $statement->bindParam(':company_id', $company['id'], PDO::PARAM_INT);

            $statement->execute();
            $statement = null;
        } else {
            if (!file_exists('../../../tmp/cmm-tech-test-errors.log')) {
                $newLogFile = fopen('../../../tmp/cmm-tech-test-errors.log', 'w');
            }

            Analog::handler(File::init('../../../tmp/cmm-tech-test-errors.log'));
            $message = 'Company "' . $company['name'] . '" (company ID: ' . $company['id'] . ') has run out of credits';
            Analog::log($message);
        }
    }

    public function addCredit(int $companyId, int $credits): void
    {
        $statement = $this->db->prepare('UPDATE companies SET credits = :credits WHERE id = :company_id');
        $statement->bindParam(':credits', $credits, PDO::PARAM_INT);
        $statement->bindParam(':company_id', $companyId, PDO::PARAM_INT);
        $statement->execute();
        $statement = null;
    }
}
