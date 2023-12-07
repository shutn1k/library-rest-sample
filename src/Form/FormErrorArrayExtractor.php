<?php

namespace App\Form;

use Symfony\Component\Form\FormInterface;

class FormErrorArrayExtractor
{
    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public function extract(FormInterface $form): array
    {

        $errors = [];

        foreach ($form->getErrors() as $error) {
            if ($form->isRoot()) {
                $errors['common'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $childForm) {
            if ($childForm->isSubmitted() && !$childForm->isValid()) {
                $errors[$childForm->getName()][] = $this->extract($childForm);
            }
        }

        return $errors;
    }
}
