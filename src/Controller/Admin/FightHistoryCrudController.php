<?php

namespace App\Controller\Admin;

use App\Entity\FightHistory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class FightHistoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FightHistory::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            AssociationField::new('adventurer'),
            AssociationField::new('monster'),

            BooleanField::new('victory'),
        ];
    }
}
