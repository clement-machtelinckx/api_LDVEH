<?php

namespace App\Controller\Admin;

use App\Entity\Feedback;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FeedbackCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Feedback::class;
    }

   
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('email'),
            TextEditorField::new('message'),
            IntegerField::new('rating'),
            DateTimeField::new('createdAt')->hideOnForm(),
            TextField::new('status'),
        ];
    }
    
}
