import React, { useState } from 'react';
import { Card, CardBody, CardHeader, Row, Col, Button, Badge, Form, Table, Alert } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import Layout from "../../../Layouts";
import { useTranslation } from 'react-i18next';

const Billing = () => {
    const { t } = useTranslation();
    const [invoices] = useState([
        {
            id: 'INV-2024-001',
            date: '2024-01-15',
            dueDate: '2024-02-15',
            amount: 1250.00,
            status: 'Paid',
            description: t('Power Monitoring Service - January 2024'),
            items: [
                { name: t('Power Monitoring Service'), quantity: 1, price: 1000.00 },
                { name: t('Additional Device Support'), quantity: 2, price: 125.00 }
            ]
        },
        {
            id: 'INV-2024-002',
            date: '2024-01-01',
            dueDate: '2024-02-01',
            amount: 950.00,
            status: 'Pending',
            description: t('Power Monitoring Service - December 2023'),
            items: [
                { name: t('Power Monitoring Service'), quantity: 1, price: 800.00 },
                { name: t('Data Storage Upgrade'), quantity: 1, price: 150.00 }
            ]
        },
        {
            id: 'INV-2023-012',
            date: '2023-12-15',
            dueDate: '2024-01-15',
            amount: 800.00,
            status: 'Paid',
            description: t('Power Monitoring Service - December 2023'),
            items: [
                { name: t('Power Monitoring Service'), quantity: 1, price: 800.00 }
            ]
        }
    ]);

    const [paymentMethods] = useState([
        {
            id: 1,
            type: t('Credit Card'),
            last4: '4242',
            expiry: '12/25',
            isDefault: true
        },
        {
            id: 2,
            type: t('Bank Transfer'),
            account: '****1234',
            bank: t('Tejarat Bank'),
            isDefault: false
        }
    ]);

    const getStatusBadge = (status: string) => {
        const variants: { [key: string]: string } = {
            'Paid': 'success',
            'Pending': 'warning',
            'Overdue': 'danger',
            'Cancelled': 'secondary'
        };
        const statusText = status === 'Paid' ? t('Paid') :
                          status === 'Pending' ? t('Pending') :
                          status === 'Overdue' ? t('Overdue') :
                          status === 'Cancelled' ? t('Cancelled') : status;
        return <Badge bg={variants[status] || 'secondary'}>{statusText}</Badge>;
    };

    const totalPaid = invoices.filter(inv => inv.status === 'Paid').reduce((sum, inv) => sum + inv.amount, 0);
    const totalPending = invoices.filter(inv => inv.status === 'Pending').reduce((sum, inv) => sum + inv.amount, 0);

    return (
        <React.Fragment>
            <Head title={t('Billing') + ' | ' + t('power_dashboard.pages.dashboard.title')} />
            <div className="page-content">
                <div className="container-fluid">
                    <BreadCrumb title={t('Billing')} pageTitle={t('Dashboard')} />
                    
                    <Row>
                        <Col lg={3}>
                            <Card>
                                <CardBody>
                                    <div className="d-flex align-items-center">
                                        <div className="flex-shrink-0">
                                            <div className="avatar-sm rounded">
                                                <span className="avatar-title bg-success-subtle text-success rounded fs-3">
                                                    <i className="ri-money-dollar-circle-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-1">{t('Total Paid')}</h6>
                                            <h4 className="mb-0 text-success">${totalPaid.toLocaleString()}</h4>
                                        </div>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={3}>
                            <Card>
                                <CardBody>
                                    <div className="d-flex align-items-center">
                                        <div className="flex-shrink-0">
                                            <div className="avatar-sm rounded">
                                                <span className="avatar-title bg-warning-subtle text-warning rounded fs-3">
                                                    <i className="ri-time-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-1">{t('Pending')}</h6>
                                            <h4 className="mb-0 text-warning">${totalPending.toLocaleString()}</h4>
                                        </div>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={3}>
                            <Card>
                                <CardBody>
                                    <div className="d-flex align-items-center">
                                        <div className="flex-shrink-0">
                                            <div className="avatar-sm rounded">
                                                <span className="avatar-title bg-info-subtle text-info rounded fs-3">
                                                    <i className="ri-file-list-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-1">{t('Total Invoices')}</h6>
                                            <h4 className="mb-0 text-info">{invoices.length}</h4>
                                        </div>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={3}>
                            <Card>
                                <CardBody>
                                    <div className="d-flex align-items-center">
                                        <div className="flex-shrink-0">
                                            <div className="avatar-sm rounded">
                                                <span className="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                                    <i className="ri-calendar-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-1">{t('Next Due')}</h6>
                                            <h4 className="mb-0 text-primary">{t('Feb 15')}</h4>
                                        </div>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>

                    <Row>
                        <Col lg={8}>
                            <Card>
                                <CardHeader>
                                    <div className="d-flex align-items-center justify-content-between">
                                        <h4 className="card-title mb-0">{t('Invoice History')}</h4>
                                        <Button variant="primary" size="sm">
                                            <i className="ri-download-line me-1"></i>
                                            {t('Export All')}
                                        </Button>
                                    </div>
                                </CardHeader>
                                <CardBody>
                                    <div className="table-responsive">
                                        <Table className="table-nowrap align-middle">
                                            <thead className="table-light">
                                                <tr>
                                                    <th>{t('Invoice #')}</th>
                                                    <th>{t('Date')}</th>
                                                    <th>{t('Due Date')}</th>
                                                    <th>{t('Amount')}</th>
                                                    <th>{t('Status')}</th>
                                                    <th>{t('Action')}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {invoices.map((invoice, index) => (
                                                    <tr key={index}>
                                                        <td>
                                                            <span className="fw-medium">{invoice.id}</span>
                                                        </td>
                                                        <td>{invoice.date}</td>
                                                        <td>{invoice.dueDate}</td>
                                                        <td>${invoice.amount.toLocaleString()}</td>
                                                        <td>{getStatusBadge(invoice.status)}</td>
                                                        <td>
                                                            <div className="dropdown">
                                                                <Button variant="outline-secondary" size="sm" className="dropdown-toggle" data-bs-toggle="dropdown">
                                                                    {t('Actions')}
                                                                </Button>
                                                                <ul className="dropdown-menu">
                                                                    <li><a className="dropdown-item" href="#"><i className="ri-eye-line me-2"></i>{t('View Invoice')}</a></li>
                                                                    <li><a className="dropdown-item" href="#"><i className="ri-download-line me-2"></i>{t('Download PDF')}</a></li>
                                                                    {invoice.status === 'Pending' && (
                                                                        <li><a className="dropdown-item" href="#"><i className="ri-bank-card-line me-2"></i>{t('Pay Now')}</a></li>
                                                                    )}
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </Table>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={4}>
                            <Card>
                                <CardHeader>
                                    <h5 className="card-title mb-0">{t('Payment Methods')}</h5>
                                </CardHeader>
                                <CardBody>
                                    {paymentMethods.map((method, index) => (
                                        <div key={index} className="d-flex align-items-center justify-content-between mb-3">
                                            <div className="d-flex align-items-center">
                                                <div className="avatar-sm me-3">
                                                    <span className="avatar-title bg-light rounded">
                                                        <i className="ri-bank-card-line text-primary"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 className="mb-1">{method.type}</h6>
                                                    <p className="text-muted mb-0">
                                                        {method.type === t('Credit Card') ? `**** ${method.last4}` : `${method.bank} ${method.account}`}
                                                    </p>
                                                </div>
                                            </div>
                                            <div>
                                                {method.isDefault && (
                                                    <Badge bg="success" className="me-2">{t('Default')}</Badge>
                                                )}
                                                <Button variant="outline-secondary" size="sm">
                                                    <i className="ri-settings-3-line"></i>
                                                </Button>
                                            </div>
                                        </div>
                                    ))}
                                    <Button variant="outline-primary" className="w-100">
                                        <i className="ri-add-line me-1"></i>
                                        {t('Add Payment Method')}
                                    </Button>
                                </CardBody>
                            </Card>

                            <Card className="mt-3">
                                <CardHeader>
                                    <h5 className="card-title mb-0">{t('Billing Information')}</h5>
                                </CardHeader>
                                <CardBody>
                                    <div className="mb-3">
                                        <h6>{t('RCPSS-SUTech')}</h6>
                                        <p className="text-muted mb-1">{t('Power Monitoring Division')}</p>
                                        <p className="text-muted mb-1">{t('Tehran, Iran')}</p>
                                        <p className="text-muted mb-0">support@rcpss-sutech.com</p>
                                    </div>
                                    <Alert variant="info">
                                        <i className="ri-information-line me-2"></i>
                                        {t('For billing inquiries, please contact our support team.')}
                                    </Alert>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>

                    <Row>
                        <Col lg={12}>
                            <Card>
                                <CardHeader>
                                    <h5 className="card-title mb-0">{t('Upcoming Billing')}</h5>
                                </CardHeader>
                                <CardBody>
                                    <div className="row">
                                        <div className="col-md-6">
                                            <h6>{t('Next Invoice')}</h6>
                                            <p className="text-muted">{t('Estimated amount for February 2024')}</p>
                                            <h4 className="text-primary">$1,200.00</h4>
                                            <small className="text-muted">{t('Due: February 15, 2024')}</small>
                                        </div>
                                        <div className="col-md-6">
                                            <h6>{t('Service Plan')}</h6>
                                            <p className="text-muted">{t('Current subscription details')}</p>
                                            <div className="d-flex justify-content-between">
                                                <span>{t('Power Monitoring Service')}</span>
                                                <span className="fw-medium">$800/month</span>
                                            </div>
                                            <div className="d-flex justify-content-between">
                                                <span>{t('Additional Devices (2)')}</span>
                                                <span className="fw-medium">$200/month</span>
                                            </div>
                                            <div className="d-flex justify-content-between">
                                                <span>{t('Data Storage')}</span>
                                                <span className="fw-medium">$200/month</span>
                                            </div>
                                            <hr />
                                            <div className="d-flex justify-content-between fw-bold">
                                                <span>{t('Total')}</span>
                                                <span>$1,200/month</span>
                                            </div>
                                </div>
                            </div>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>
                </div>
            </div>
        </React.Fragment>
    );
};

Billing.layout = (page: any) => <Layout children={page} />
export default Billing;
