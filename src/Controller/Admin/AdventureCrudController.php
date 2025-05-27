<?php

namespace App\Controller\Admin;

use App\Entity\Adventure;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class AdventureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Adventure::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            AssociationField::new('user'),
            AssociationField::new('book'),
            AssociationField::new('adventurer'),
            AssociationField::new('currentPage'),
            AssociationField::new('fromLastPage')->hideOnIndex(),

            DateTimeField::new('startedAt')->hideOnForm(),
            DateTimeField::new('endedAt')->hideOnIndex(),

            BooleanField::new('isFinished'),
        ];
    }
}
