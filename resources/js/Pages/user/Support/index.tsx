import React, { useState } from 'react';
import { Card, CardBody, CardHeader, Row, Col, Button, Badge, Form, InputGroup, Alert } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import Layout from "../../../Layouts";
import { useTranslation } from 'react-i18next';

const Support = () => {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState('tickets');
    const [tickets] = useState([
        {
            id: 'TKT-001',
            subject: 'Device PM-001 Connection Issue',
            status: 'Open',
            priority: 'High',
            category: 'Technical',
            created: '2024-01-15',
            lastUpdate: '2024-01-16',
            assignedTo: 'Support Team'
        },
        {
            id: 'TKT-002',
            subject: 'Power Consumption Report Generation',
            status: 'In Progress',
            priority: 'Medium',
            category: 'Feature Request',
            created: '2024-01-14',
            lastUpdate: '2024-01-15',
            assignedTo: 'Development Team'
        },
        {
            id: 'TKT-003',
            subject: 'Dashboard Performance Optimization',
            status: 'Resolved',
            priority: 'Low',
            category: 'Bug Report',
            created: '2024-01-10',
            lastUpdate: '2024-01-12',
            assignedTo: 'Support Team'
        }
    ]);

    const [faqs] = useState([
        {
            question: 'How do I add a new power monitoring device?',
            answer: 'Navigate to Devices > Add Device, enter the device details and configuration parameters. The device will be automatically detected once connected to the network.'
        },
        {
            question: 'What should I do if a device goes offline?',
            answer: 'Check the device power supply and network connection. If the issue persists, contact support with the device ID and error logs.'
        },
        {
            question: 'How can I export power consumption reports?',
            answer: 'Go to Analytics > Reports, select your desired date range and metrics, then click the Export button to download in PDF or Excel format.'
        },
        {
            question: 'How do I set up alert notifications?',
            answer: 'Navigate to Alerts > Configuration, set your threshold values and notification preferences. You can receive alerts via email, SMS, or dashboard notifications.'
        }
    ]);

    const getStatusBadge = (status: string) => {
        const variants: { [key: string]: string } = {
            'Open': 'warning',
            'In Progress': 'info',
            'Resolved': 'success',
            'Closed': 'secondary'
        };
        return <Badge bg={variants[status] || 'secondary'}>{status}</Badge>;
    };

    const getPriorityBadge = (priority: string) => {
        const variants: { [key: string]: string } = {
            'High': 'danger',
            'Medium': 'warning',
            'Low': 'info'
        };
        return <Badge bg={variants[priority] || 'secondary'}>{priority}</Badge>;
    };

    return (
        <React.Fragment>
            <Head title={t('Support') + ' | ' + t('power_dashboard.pages.dashboard.title')} />
            <div className="page-content">
                <div className="container-fluid">
                    <BreadCrumb title={t('Support')} pageTitle={t('Dashboard')} />
                    
                    <Row>
                        <Col lg={12}>
                            <Card>
                                <CardHeader>
                                    <div className="d-flex align-items-center justify-content-between">
                                        <h4 className="card-title mb-0">{t('Support')}</h4>
                                        <div>
                                            <Button variant="outline-primary" size="sm" className="me-2" href="/dashboard/support/ticket-chat">
                                                <i className="ri-message-2-line align-middle me-1"></i>
                                                {t('Open Chat')}
                                            </Button>
                                            <Button variant="primary" size="sm">
                                                <i className="ri-add-line align-middle me-1"></i>
                                                {t('New Ticket')}
                                            </Button>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardBody>
                                    <div className="d-flex border-bottom mb-3">
                                        <Button
                                            variant={activeTab === 'tickets' ? 'primary' : 'light'}
                                            className="me-2"
                                            onClick={() => setActiveTab('tickets')}
                                        >
                                            {t('My Tickets')}
                                        </Button>
                                        <Button
                                            variant={activeTab === 'faq' ? 'primary' : 'light'}
                                            className="me-2"
                                            onClick={() => setActiveTab('faq')}
                                        >
                                            {t('FAQ')}
                                        </Button>
                                        <Button
                                            variant={activeTab === 'contact' ? 'primary' : 'light'}
                                            onClick={() => setActiveTab('contact')}
                                        >
                                            {t('Contact Support')}
                                        </Button>
                                    </div>

                                    {activeTab === 'tickets' && (
                                        <div>
                                            <div className="table-responsive">
                                                <table className="table table-nowrap align-middle">
                                                    <thead className="table-light">
                                                        <tr>
                                                            <th>{t('Ticket ID')}</th>
                                                            <th>{t('Subject')}</th>
                                                            <th>{t('Status')}</th>
                                                            <th>{t('Priority')}</th>
                                                            <th>{t('Category')}</th>
                                                            <th>{t('Created')}</th>
                                                            <th>{t('Last Update')}</th>
                                                            <th>{t('Assigned To')}</th>
                                                            <th>{t('Action')}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {tickets.map((ticket, index) => (
                                                            <tr key={index}>
                                                                <td>
                                                                    <span className="fw-medium">{ticket.id}</span>
                                                                </td>
                                                                <td>{ticket.subject}</td>
                                                                <td>{getStatusBadge(ticket.status)}</td>
                                                                <td>{getPriorityBadge(ticket.priority)}</td>
                                                                <td>{ticket.category}</td>
                                                                <td>{ticket.created}</td>
                                                                <td>{ticket.lastUpdate}</td>
                                                                <td>{ticket.assignedTo}</td>
                                                                <td>
                                                                    <Button variant="outline-primary" size="sm">
                                                                        {t('View')}
                                                                    </Button>
                                                                </td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    )}

                                    {activeTab === 'faq' && (
                                        <div>
                                            <Row>
                                                {faqs.map((faq, index) => (
                                                    <Col lg={6} key={index}>
                                                        <Card className="mb-3">
                                                            <CardBody>
                                                                <h6 className="card-title mb-2">{faq.question}</h6>
                                                                <p className="card-text text-muted">{faq.answer}</p>
                                                            </CardBody>
                                                        </Card>
                                                    </Col>
                                                ))}
                                            </Row>
                                        </div>
                                    )}

                                    {activeTab === 'contact' && (
                                        <div>
                                            <Row>
                                                <Col lg={8}>
                                                    <Card>
                                                        <CardHeader>
                                                            <h5 className="card-title mb-0">{t('Send Message to Support')}</h5>
                                                        </CardHeader>
                                                        <CardBody>
                                                            <Form>
                                                                <Row>
                                                                    <Col md={6}>
                                                                        <Form.Group className="mb-3">
                                                                            <Form.Label>{t('Subject')}</Form.Label>
                                                                            <Form.Control type="text" placeholder={t('Enter subject')} />
                                                                        </Form.Group>
                                                                    </Col>
                                                                    <Col md={6}>
                                                                        <Form.Group className="mb-3">
                                                                            <Form.Label>{t('Category')}</Form.Label>
                                                                            <Form.Select>
                                                                                <option>{t('Technical Issue')}</option>
                                                                                <option>{t('Feature Request')}</option>
                                                                                <option>{t('Bug Report')}</option>
                                                                                <option>{t('General Inquiry')}</option>
                                                                            </Form.Select>
                                                                        </Form.Group>
                                                                    </Col>
                                                                </Row>
                                                                <Form.Group className="mb-3">
                                                                    <Form.Label>{t('Priority')}</Form.Label>
                                                                    <Form.Select>
                                                                        <option>{t('Low')}</option>
                                                                        <option>{t('Medium')}</option>
                                                                        <option>{t('High')}</option>
                                                                        <option>{t('Critical')}</option>
                                                                    </Form.Select>
                                                                </Form.Group>
                                                                <Form.Group className="mb-3">
                                                                    <Form.Label>{t('Message')}</Form.Label>
                                                                    <Form.Control as="textarea" rows={5} placeholder={t('Describe your issue or request...')} />
                                                                </Form.Group>
                                                                <Button variant="primary" type="submit">
                                                                    {t('Send Message')}
                                                                </Button>
                                                            </Form>
                                                        </CardBody>
                                                    </Card>
                                                </Col>
                                                <Col lg={4}>
                                                    <Card>
                                                        <CardHeader>
                                                            <h5 className="card-title mb-0">{t('Contact Information')}</h5>
                                                        </CardHeader>
                                                        <CardBody>
                                                            <div className="mb-3">
                                                                <h6>{t('Email Support')}</h6>
                                                                <p className="text-muted mb-0">{t('support@rcpss-sutech.com')}</p>
                                                            </div>
                                                            <div className="mb-3">
                                                                <h6>{t('Phone Support')}</h6>
                                                                <p className="text-muted mb-0">{t('+98 21 1234 5678')}</p>
                                                            </div>
                                                            <div className="mb-3">
                                                                <h6>{t('Office Hours')}</h6>
                                                                <p className="text-muted mb-0">{t('Monday - Friday: 8:00 AM - 6:00 PM')}</p>
                                                            </div>
                                                            <Alert variant="info">
                                                                <i className="ri-information-line me-2"></i>
                                                                {t('For urgent issues, please call our emergency support line.')}
                                                            </Alert>
                                                        </CardBody>
                                                    </Card>
                                                </Col>
                                            </Row>
                                        </div>
                                    )}
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>
                </div>
            </div>
        </React.Fragment>
    );
};

Support.layout = (page: any) => <Layout children={page} />
export default Support;
