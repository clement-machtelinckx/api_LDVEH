<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Entity\Choice;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // Cacher l'ID lors de la création
            TextField::new('content', 'Contenu de la page'),
            IntegerField::new('pageNumber', 'Numéro de page'),
            AssociationField::new('book', 'Livre associé'),

            // Ajouter directement les Choice liés à cette Page
            CollectionField::new('choices', 'Choix possibles')
                ->useEntryCrudForm() // Utilisation du CRUD EasyAdmin pour gérer les choix
                ->setFormTypeOptions([
                    'by_reference' => false,
                    'allow_add' => true
                ])
        ];
    }
}
