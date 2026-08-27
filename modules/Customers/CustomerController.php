<?php

declare(strict_types=1);

namespace Modules\Customers;

use Core\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Core\Session\Session;

final class CustomerController extends Controller
{
    private CustomerService $service;

    public function __construct(
        CustomerService $service
    ) {
        $this->service = $service;
    }


    /**
     * List customers.
     */
    public function index(): Response
    {
        return $this->view(
            'customers/index',
            [
                'title' => 'Customers',
                'customers' => $this->service->all(),
                'success' => Session::pull('customers.success'),
                'error' => Session::pull('customers.error')
            ]
        );
    }


    /**
     * Display create form.
     */
    public function create(): Response
    {
        return $this->view(
            'customers/create',
            [
                'title' => 'Create Customer',
                'errors' => Session::pull('customers.errors'),
                'old' => Session::pull('customers.old')
            ]
        );
    }


    /**
     * Store customer.
     */
    public function store(): Response
    {
        $input = Request::post();

        $result = $this->service->create(
            $input
        );

        if (!$result['success']) {

            Session::flash(
                'customers.errors',
                $result['errors']
            );

            Session::flash(
                'customers.old',
                $input
            );

            return $this->redirect(
                'customers/create'
            );
        }

        Session::flash(
            'customers.success',
            'Customer created successfully.'
        );

        return $this->redirect(
            'customers'
        );
    }


    /**
     * Display customer.
     */
    public function show(
        int $id
    ): Response {
        $customer = $this->service->find($id);

        if ($customer === null) {
            return Response::make(
                'Customer not found.',
                404
            );
        }

        return $this->view(
            'customers/show',
            [
                'title' => 'Customer Details',
                'customer' => $customer,
                'success' => Session::pull('customers.success'),
                'error' => Session::pull('customers.error')
            ]
        );
    }
}