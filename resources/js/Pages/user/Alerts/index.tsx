import React, { useState } from 'react';
import { Card, CardBody, CardHeader, Row, Col, Button, Badge, Form, Alert, Dropdown } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import Layout from "../../../Layouts";
import { useTranslation } from 'react-i18next';

const Alerts = () => {
    const { t } = useTranslation();
    const [alerts] = useState([
        {
            id: 'ALT-001',
            title: 'High Power Consumption Alert',
            description: 'Device PM-001 exceeded power consumption threshold of 2000W',
            severity: 'High',
            status: 'Active',
            device: 'PM-001 - Main Building',
            timestamp: '2024-01-16 14:30:25',
            acknowledged: false,
            category: 'Power Consumption'
        },
        {
            id: 'ALT-002',
            title: 'Device Offline',
            description: 'Device PM-003 has been offline for more than 10 minutes',
            severity: 'Medium',
            status: 'Active',
            device: 'PM-003 - Server Room',
            timestamp: '2024-01-16 14:15:10',
            acknowledged: true,
            category: 'Device Status'
        },
        {
            id: 'ALT-003',
            title: 'Low Efficiency Warning',
            description: 'Device PM-002 efficiency dropped below 85%',
            severity: 'Low',
            status: 'Resolved',
            device: 'PM-002 - Lab A',
            timestamp: '2024-01-16 13:45:30',
            acknowledged: true,
            category: 'Efficiency'
        },
        {
            id: 'ALT-004',
            title: 'Voltage Fluctuation',
            description: 'Unusual voltage fluctuations detected on device PM-004',
            severity: 'High',
            status: 'Active',
            device: 'PM-004 - Workshop',
            timestamp: '2024-01-16 14:25:15',
            acknowledged: false,
            category: 'Electrical'
        }
    ]);

    const [filterStatus, setFilterStatus] = useState('all');
    const [filterSeverity, setFilterSeverity] = useState('all');

    const getSeverityBadge = (severity: string) => {
        const variants: { [key: string]: string } = {
            'High': 'danger',
            'Medium': 'warning',
            'Low': 'info'
        };
        return <Badge bg={variants[severity] || 'secondary'}>{severity}</Badge>;
    };

    const getStatusBadge = (status: string) => {
        const variants: { [key: string]: string } = {
            'Active': 'danger',
            'Acknowledged': 'warning',
            'Resolved': 'success',
            'Cleared': 'secondary'
        };
        return <Badge bg={variants[status] || 'secondary'}>{status}</Badge>;
    };

    const filteredAlerts = alerts.filter(alert => {
        if (filterStatus !== 'all' && alert.status !== filterStatus) return false;
        if (filterSeverity !== 'all' && alert.severity !== filterSeverity) return false;
        return true;
    });

    const activeAlerts = alerts.filter(alert => alert.status === 'Active').length;
    const highSeverityAlerts = alerts.filter(alert => alert.severity === 'High').length;

    return (
        <React.Fragment>
            <Head title={t('Alerts') + ' | ' + t('power_dashboard.pages.dashboard.title')} />
            <div className="page-content">
                <div className="container-fluid">
                    <BreadCrumb title={t('Alerts')} pageTitle={t('Dashboard')} />
                    
                    <Row>
                        <Col lg={3}>
                            <Card>
                                <CardBody>
                                    <div className="d-flex align-items-center">
                                        <div className="flex-shrink-0">
                                            <div className="avatar-sm rounded">
                                                <span className="avatar-title bg-danger-subtle text-danger rounded fs-3">
                                                    <i className="ri-error-warning-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-1">Active Alerts</h6>
                                            <h4 className="mb-0 text-danger">{activeAlerts}</h4>
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
                                                    <i className="ri-alert-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-1">High Severity</h6>
                                            <h4 className="mb-0 text-warning">{highSeverityAlerts}</h4>
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
                                                <span className="avatar-title bg-success-subtle text-success rounded fs-3">
                                                    <i className="ri-check-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-1">Resolved</h6>
                                            <h4 className="mb-0 text-success">{alerts.filter(a => a.status === 'Resolved').length}</h4>
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
                                                    <i className="ri-settings-3-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-1">Total Alerts</h6>
                                            <h4 className="mb-0 text-info">{alerts.length}</h4>
                                        </div>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>

                    <Row>
                        <Col lg={12}>
                            <Card>
                                <CardHeader>
                                    <div className="d-flex align-items-center justify-content-between">
                                        <h4 className="card-title mb-0">Alert Management</h4>
                                        <div className="d-flex gap-2">
                                            <Button variant="outline-primary" size="sm">
                                                <i className="ri-settings-3-line me-1"></i>
                                                Alert Settings
                                            </Button>
                                            <Button variant="primary" size="sm">
                                                <i className="ri-add-line me-1"></i>
                                                Create Alert Rule
                                            </Button>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardBody>
                                    <Row className="mb-3">
                                        <Col md={4}>
                                            <Form.Group>
                                                <Form.Label>Filter by Status</Form.Label>
                                                <Form.Select value={filterStatus} onChange={(e) => setFilterStatus(e.target.value)}>
                                                    <option value="all">All Status</option>
                                                    <option value="Active">Active</option>
                                                    <option value="Acknowledged">Acknowledged</option>
                                                    <option value="Resolved">Resolved</option>
                                                    <option value="Cleared">Cleared</option>
                                                </Form.Select>
                                            </Form.Group>
                                        </Col>
                                        <Col md={4}>
                                            <Form.Group>
                                                <Form.Label>Filter by Severity</Form.Label>
                                                <Form.Select value={filterSeverity} onChange={(e) => setFilterSeverity(e.target.value)}>
                                                    <option value="all">All Severity</option>
                                                    <option value="High">High</option>
                                                    <option value="Medium">Medium</option>
                                                    <option value="Low">Low</option>
                                                </Form.Select>
                                            </Form.Group>
                                        </Col>
                                        <Col md={4}>
                                            <Form.Group>
                                                <Form.Label>&nbsp;</Form.Label>
                                                <div className="d-flex gap-2">
                                                    <Button variant="outline-secondary" size="sm" className="w-100">
                                                        <i className="ri-refresh-line me-1"></i>
                                                        Refresh
                                                    </Button>
                                                    <Button variant="outline-success" size="sm" className="w-100">
                                                        <i className="ri-download-line me-1"></i>
                                                        Export
                                                    </Button>
                                                </div>
                                            </Form.Group>
                                        </Col>
                                    </Row>

                                    <div className="table-responsive">
                                        <table className="table table-nowrap align-middle">
                                            <thead className="table-light">
                                                <tr>
                                                    <th>Alert ID</th>
                                                    <th>Title</th>
                                                    <th>Severity</th>
                                                    <th>Status</th>
                                                    <th>Device</th>
                                                    <th>Category</th>
                                                    <th>Timestamp</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {filteredAlerts.map((alert, index) => (
                                                    <tr key={index}>
                                                        <td>
                                                            <span className="fw-medium">{alert.id}</span>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <h6 className="mb-1">{alert.title}</h6>
                                                                <p className="text-muted mb-0 small">{alert.description}</p>
                                                            </div>
                                                        </td>
                                                        <td>{getSeverityBadge(alert.severity)}</td>
                                                        <td>{getStatusBadge(alert.status)}</td>
                                                        <td>{alert.device}</td>
                                                        <td>{alert.category}</td>
                                                        <td>{alert.timestamp}</td>
                                                        <td>
                                                            <div className="dropdown">
                                                                <Button variant="outline-secondary" size="sm" className="dropdown-toggle" data-bs-toggle="dropdown">
                                                                    Actions
                                                                </Button>
                                                                <ul className="dropdown-menu">
                                                                    <li><a className="dropdown-item" href="#"><i className="ri-eye-line me-2"></i>View Details</a></li>
                                                                    <li><a className="dropdown-item" href="#"><i className="ri-check-line me-2"></i>Acknowledge</a></li>
                                                                    <li><a className="dropdown-item" href="#"><i className="ri-close-line me-2"></i>Resolve</a></li>
                                                                    <li><hr className="dropdown-divider" /></li>
                                                                    <li><a className="dropdown-item" href="#"><i className="ri-settings-3-line me-2"></i>Configure Rule</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>

                    <Row>
                        <Col lg={6}>
                            <Card>
                                <CardHeader>
                                    <h5 className="card-title mb-0">Alert Configuration</h5>
                                </CardHeader>
                                <CardBody>
                                    <Form>
                                        <Form.Group className="mb-3">
                                            <Form.Label>Power Consumption Threshold (W)</Form.Label>
                                            <Form.Control type="number" defaultValue="2000" />
                                        </Form.Group>
                                        <Form.Group className="mb-3">
                                            <Form.Label>Efficiency Threshold (%)</Form.Label>
                                            <Form.Control type="number" defaultValue="85" />
                                        </Form.Group>
                                        <Form.Group className="mb-3">
                                            <Form.Label>Device Offline Timeout (minutes)</Form.Label>
                                            <Form.Control type="number" defaultValue="10" />
                                        </Form.Group>
                                        <Form.Group className="mb-3">
                                            <Form.Check type="switch" label="Email Notifications" defaultChecked />
                                        </Form.Group>
                                        <Form.Group className="mb-3">
                                            <Form.Check type="switch" label="SMS Notifications" />
                                        </Form.Group>
                                        <Button variant="primary" type="submit">
                                            Save Configuration
                                        </Button>
                                    </Form>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={6}>
                            <Card>
                                <CardHeader>
                                    <h5 className="card-title mb-0">Recent Alert History</h5>
                                </CardHeader>
                                <CardBody>
                                    <div className="timeline-alt pb-0">
                                        {alerts.slice(0, 5).map((alert, index) => (
                                            <div className="timeline-item" key={index}>
                                                <div className="timeline-badge bg-{alert.severity === 'High' ? 'danger' : alert.severity === 'Medium' ? 'warning' : 'info'}">
                                                    <i className="ri-error-warning-line"></i>
                                                </div>
                                                <div className="timeline-panel">
                                                    <div className="timeline-heading">
                                                        <h6 className="timeline-title">{alert.title}</h6>
                                                    </div>
                                                    <div className="timeline-body">
                                                        <p className="text-muted mb-0">{alert.timestamp}</p>
                                                    </div>
                                </div>
                            </div>
                                        ))}
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

Alerts.layout = (page: any) => <Layout children={page} />
export default Alerts;
