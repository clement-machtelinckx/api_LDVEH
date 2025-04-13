<?php

namespace App\Controller\Admin;

use App\Entity\Monster;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MonsterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Monster::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('MonsterName'),
            IntegerField::new('Ability'),
            IntegerField::new('Endurance'),
            // AssociationField::new('user')
        ];
    }
}
