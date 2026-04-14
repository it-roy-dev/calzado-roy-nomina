<?php

namespace App\Listeners;

use App\Enums\UserType;
use App\Events\AppMenuEvent;
use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Laravel\Menu;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Menu\Laravel\Html;

class AppMenuListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AppMenuEvent $event): void
    {
        $menu = $event->menu;

        // ══ MAIN ══
        $menu->html('<span>Principal</span>', ['class' => 'menu-title']);
        $menu->add(
            Link::toRoute('dashboard', '<i class="la la-dashboard"></i> <span>Panel de Control</span>')->setActive(route_is('dashboard'))
        );
        $activeClass = route_is(["app.chat"]) ? "active" : "";
        $menu->submenu(
            Html::raw('<a href="#" class="' . $activeClass . '"><i class="la la-cube"></i><span> Apps</span><span class="menu-arrow"></span></a>'),
            Menu::new()
                ->add(Link::toRoute('app.chat', 'Chat')->addClass(route_is(['app.chat']) ? 'active' : ''))
                ->addParentClass('submenu')
        );

        // ══ EMPLEADOS ══
        if(auth()->user()->canAny(['view-employees','view-attendances','view-departments','view-designations','view-holidays'])){
            $menu->html('<span>Empleados</span>', ['class' => 'menu-title']);
            $activeClass = route_is(['employees.index','employees.list','departments.index','designations.index','holidays.*','uniformes.*']) ? "active" : "";
            $menu->submenu(
                Html::raw('<a href="#" class="' . $activeClass . '"><i class="la la-user"></i> <span>Empleados</span><span class="menu-arrow"></span></a>'),
                Menu::new()
                    ->addParentClass('submenu')
                    ->addIfCan('view-employees', Link::toRoute('employees.list', 'Empleados')->addClass(route_is(['employees.index','employees.list']) ? 'active' : ''))
                    ->addIfCan('view-attendances', Link::toRoute('attendances.index', 'Asistencia')->addClass(route_is(['attendances.index']) ? 'active' : ''))
                    ->addIfCan('view-departments', Link::toRoute('departments.index', 'Departamentos')->addClass(route_is('departments.index') ? 'active' : ''))
                    ->addIfCan('view-designations', Link::toRoute('designations.index', 'Puestos')->addClass(route_is('designations.index') ? 'active' : ''))
                    ->addIfCan('view-holidays', Link::toRoute('holidays.index', 'Feriados')->addClass(route_is('holidays.*') ? 'active' : ''))
                    ->add(Link::toRoute('uniformes.index', 'Uniformes')->addClass(route_is('uniformes.*') ? 'active' : ''))
            );
        }

        // ══ NOMINA ══
       $menu->html('<span>Nómina</span>', ['class' => 'menu-title']);

        // Recibos — vista según rol
        if (auth()->user()->hasRole('Tienda')) {
            $storeUser = \App\Models\StoreUser::where('user_id', auth()->id())->first();
            if ($storeUser) {
                $menu->add(
                    Link::toRoute('boletas.tienda', '<i class="fa-solid fa-file-invoice"></i> <span>Mis Recibos</span>')
                        ->setActive(route_is('boletas.tienda'))
                );
            } else {
                $menu->add(
                    Link::toRoute('boletas.mis-recibos', '<i class="fa-solid fa-file-invoice"></i> <span>Mis Recibos</span>')
                        ->setActive(route_is('boletas.mis-recibos'))
                );
            }
        } else {
            $menu->add(
                Link::toRoute('boletas.index', '<i class="fa-solid fa-file-invoice"></i> <span>Recibos</span>')
                    ->setActive(route_is('boletas.*'))
            );
        }
        $nominaActive = route_is(['nomina.*']) ? 'active' : '';
        $menu->submenu(
            Html::raw('<a href="#" class="' . $nominaActive . '"><i class="la la-money"></i><span> Nómina</span><span class="menu-arrow"></span></a>'),
            Menu::new()
                ->add(Link::toRoute('nomina.index', 'Planillas')->addClass(route_is(['nomina.*']) ? 'active' : ''))
                ->addParentClass('submenu')
        );
        // ══ REPORTES ══
        $menu->submenu(
            Html::raw('<a href="#"><i class="la la-bar-chart"></i><span> Reportes</span><span class="menu-arrow"></span></a>'),
            Menu::new()
                ->addParentClass('submenu')
        );

        // ══ FINANZAS ══
        $menu->html('<span>Finanzas</span>', ['class' => 'menu-title']);
        $menu->addIfCan('view-assets', Link::toRoute('assets.index', '<i class="la la-object-ungroup"></i> <span>Activos</span>')->setActive(route_is('assets.index')));
        $accountingActive = route_is(["budget.categories.*","budgets.*","budget.expense.*","budget.revenue.*"]) ? "active" : "";
        $menu->submenu(
            Html::raw('<a href="#" class="' . $accountingActive . '"><i class="la la-files-o"></i><span> Contabilidad</span><span class="menu-arrow"></span></a>'),
            Menu::new()
                ->addIfCan('view-budgetCategories', Link::toRoute('budget.categories.index', 'Categorías')->addClass(route_is(['budget.categories.*']) ? 'active' : ''))
                ->addIfCan('view-budgets', Link::toRoute('budgets.index', 'Presupuestos')->addClass(route_is(['budgets.*']) ? 'active' : ''))
                ->addIfCan('view-budgetExpenses', Link::toRoute('budget.expense.index', 'Gastos de Presupuesto')->addClass(route_is(['budget.expense.*']) ? 'active' : ''))
                ->addIfCan('view-budgetRevenues', Link::toRoute('budget.revenue.index', 'Ingresos de Presupuesto')->addClass(route_is(['budget.revenue.*']) ? 'active' : ''))
                ->addParentClass('submenu')
        );

        // ══ SISTEMA ══
        $menu->html('<span>Sistema</span>', ['class' => 'menu-title']);
        $menu->add(Link::toRoute('tickets.index', '<i class="la la-ticket"></i> <span>Tickets</span>')->setActive(route_is('tickets.*')));
        if(auth()->user()->type === UserType::EMPLOYEE){
            $menu->add(Link::toRoute('assigned-tickets', '<i class="la la-ticket"></i> <span>Mis Tickets</span>')->setActive(route_is('assigned-tickets')));
        }
        $menu->addIfCan('view-usuarios', Link::toRoute('users.index', '<i class="la la-user-plus"></i> <span>Usuarios</span>')->setActive(route_is('users.index')));
        $menu->addIfCan('view-respaldos', Link::toRoute('backups.index', '<i class="la la-cloud-upload"></i> <span>Respaldos</span>')->setActive(route_is('backups.index')));
        $menu->addIfCan('view-ajustes', Link::toRoute('settings.index', '<i class="la la-cog"></i> <span>Ajustes</span>')->setActive(route_is('settings.index')));
        //$menu->addIfCan('view-roles', Link::toRoute('roles.index', '<i class="la la-key"></i> <span>Roles y Permisos</span>')->setActive(route_is('roles.*')));
        //$menu->addIfCan('view-assets', Link::toRoute('assets.index', '<i class="la la-object-ungroup"></i> <span>Activos</span>')->setActive(route_is('assets.index')));
    }  
}
