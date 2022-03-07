<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Calendar;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#
# @IsGranted("ROLE_ADMIN")
#
class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
        
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();
        $url = $this->adminUrlGenerator->setController(CalendarCrudController::class)->generateUrl();

        return $this->redirect($url);
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');

        
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('OPI')
            ->setTitle('<img src="../public/assets/images/logo.jpg"> ACME <span class="text-small">Corp.</span>');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Utilisateur');

        yield MenuItem::subMenu('Gestion employé', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter un employé', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Liste des employé', 'fas fa-eye', User::class)
        ]);

        yield MenuItem::section('Evenement');


        yield MenuItem::subMenu('Gestion Evenement', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter un Evenement', 'fas fa-plus', Calendar::class)->setAction(Crud::PAGE_NEW),
        ]);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}

