<?php

namespace App\Controller\Admin;

use App\Entity\Adventurer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdventurerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Adventurer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id')->hideOnForm(),
            TextField::new('AdventurerName'),
            IntegerField::new('Ability'),
            IntegerField::new('Endurance'),
            AssociationField::new('user')
        ];
    }

}
