<?php

namespace App\Controller\Admin;

use App\Entity\Choice;
use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ChoiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Choice::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // Cacher l'ID lors de l'édition
            TextField::new('text', 'Texte du choix'),

            // Sélection de la page actuelle par son numéro
            AssociationField::new('page', 'Page actuelle')
                ->setFormTypeOptions([
                    'choice_label' => 'pageNumber', // Affiche pageNumber au lieu de l'ID
                    'query_builder' => function ($repo) {
                        return $repo->createQueryBuilder('p')->orderBy('p.pageNumber', 'ASC');
                    },
                ]),

            // Sélection de la nextPage par son numéro
            AssociationField::new('nextPage', 'Page suivante')
                ->setFormTypeOptions([
                    'choice_label' => 'pageNumber', // Affiche pageNumber au lieu de l'ID
                    'query_builder' => function ($repo) {
                        return $repo->createQueryBuilder('p')->orderBy('p.pageNumber', 'ASC');
                    },
                ])->setRequired(false), // La page suivante est optionnelle
        ];
    }
}
