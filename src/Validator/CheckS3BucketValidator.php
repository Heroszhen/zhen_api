<?php

namespace App\Validator;

use App\Service\S3Service;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\CheckS3Bucket;

class CheckS3BucketValidator extends ConstraintValidator
{
    private S3Service $s3Service;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    public function validate($value, Constraint $constraint)
    { 
        if (null === $value || '' === $value) {
            return;
        }

        if ($this->s3Service->hasBucket($value)) {
            return;
        }

        /* @var CheckS3Bucket $constraint */
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
