<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use App\Enum\EquipmentType;
use App\Enum\EquipmentSlot;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class EquipmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Equipment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextField::new('slug', 'Slug');

        if ($pageName === Crud::PAGE_INDEX || $pageName === Crud::PAGE_DETAIL) {
            yield TextField::new('typeLabel', 'Type');
            yield TextField::new('slotLabel', 'Emplacement');
        } else {
            yield Field::new('type', 'Type')
                ->setFormType(EnumType::class)
                ->setFormTypeOptions([
                    'class' => EquipmentType::class,
                    'choice_label' => fn(EquipmentType $type) => $type->label(),
                ]);
            yield Field::new('slot', 'Emplacement')
                ->setFormType(EnumType::class)
                ->setFormTypeOptions([
                    'class' => EquipmentSlot::class,
                    'choice_label' => fn(EquipmentSlot $slot) => $slot->label(),
                    'required' => false,
                    'placeholder' => 'Aucun',
                ]);
        }

        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield IntegerField::new('enduranceBonus', 'Bonus Endurance');
        yield IntegerField::new('healAmount', 'Soin');
    }
}
