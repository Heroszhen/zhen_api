<?php

namespace App\Validator;

use App\Entity\Email\Email;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email as ConstraintsEmail;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CheckEmailValidator extends ConstraintValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Email) {
            return;
        }

        if (null !== $value->getToEmail()) {
            if (
                !isset($value->getToEmail()['email']) || 
                '' === $value->getToEmail()['email']
            ) {
                $this->buildViolation('No E-mail Destination', 'toEmail');
            } else {
                if (!$this->checkValidatedEmail($value->getToEmail()['email'])) {
                    $this->buildViolation('No validated E-mail', 'toEmail');
                }
            }
        } 

        if (null !== $value->getCcEmail()) {
            foreach($value->getCcEmail() as $key => $cc) {
                if (
                    !isset($cc['email']) ||
                    '' === $cc['email']
                ) {
                    $this->buildViolation("No E-mail Destination in line {$key}", 'ccEmail');
                } else {
                    if (!$this->checkValidatedEmail($cc['email'])) {
                        $this->buildViolation("No validated E-mail in line {$key}", 'ccEmail');
                    }
                }
            }
        } 
    }

    private function buildViolation(string $message, string $path): void
    {
        $this->context
            ->buildViolation($message)
            ->atPath($path)
            ->addViolation();
    }

    private function checkValidatedEmail(string $email): bool
    {
        $emailConstraint = new ConstraintsEmail();
        $errors = $this->validator->validate(
            $email,
            $emailConstraint 
        );
        
        return 0 === count($errors) ? true : false;
    }
}
