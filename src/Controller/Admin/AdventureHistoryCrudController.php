<?php

namespace App\Controller\Admin;

use App\Entity\AdventureHistory;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdventureHistoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdventureHistory::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('book', 'Livre associé'),
            TextField::new('bookTitle', 'Titre du livre'),
            TextField::new('adventurerName', 'Nom de l’aventurier'),
            DateTimeField::new('finishAt', 'Date de fin'),
        ];
    }

}
