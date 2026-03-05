<?php

namespace App\Controller\Admin;

use App\Entity\AdventurerEquipment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class AdventurerEquipmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdventurerEquipment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Inventaire')
            ->setEntityLabelInPlural('Inventaire (Aventurier ↔ Équipement)')
            ->setDefaultSort(['adventurer' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('adventurer', 'Aventurier');
        yield AssociationField::new('equipment', 'Équipement');
        yield IntegerField::new('quantity', 'Quantité')->setFormTypeOptions([
            'attr' => ['min' => 1],
        ]);
    }
}
