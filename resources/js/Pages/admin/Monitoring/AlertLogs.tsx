import React, { useState } from 'react';
import { Card, Col, Container, Row, Badge, Table, Button, Dropdown, Form, InputGroup } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import Layout from '../../../Layouts';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import FeatherIcon from 'feather-icons-react';
import { useTranslation } from 'react-i18next';

const AlertLogs = () => {
    const { t } = useTranslation();
    const [alerts, setAlerts] = useState([
        {
            id: 1,
            device: 'Power Meter #001',
            type: 'High Power Consumption',
            severity: 'high',
            message: 'Power consumption exceeded 90% threshold',
            timestamp: '2024-01-15 14:30:25',
            status: 'active',
            location: 'Building A - Floor 1'
        },
        {
            id: 2,
            device: 'Power Meter #003',
            type: 'Device Offline',
            severity: 'critical',
            message: 'Device connection lost for more than 10 minutes',
            timestamp: '2024-01-15 14:25:10',
            status: 'resolved',
            location: 'Building B - Floor 1'
        },
        {
            id: 3,
            device: 'Power Meter #004',
            type: 'Voltage Fluctuation',
            severity: 'medium',
            message: 'Voltage dropped below 200V threshold',
            timestamp: '2024-01-15 14:20:45',
            status: 'active',
            location: 'Building B - Floor 2'
        },
        {
            id: 4,
            device: 'Power Meter #002',
            type: 'Temperature Warning',
            severity: 'low',
            message: 'Device temperature approaching limit',
            timestamp: '2024-01-15 14:15:30',
            status: 'acknowledged',
            location: 'Building A - Floor 2'
        }
    ]);

    const getSeverityBadge = (severity: string) => {
        switch (severity) {
            case 'critical':
                return <Badge bg="danger">Critical</Badge>;
            case 'high':
                return <Badge bg="warning">High</Badge>;
            case 'medium':
                return <Badge bg="info">Medium</Badge>;
            case 'low':
                return <Badge bg="success">Low</Badge>;
            default:
                return <Badge bg="secondary">Unknown</Badge>;
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge bg="danger">Active</Badge>;
            case 'resolved':
                return <Badge bg="success">Resolved</Badge>;
            case 'acknowledged':
                return <Badge bg="warning">Acknowledged</Badge>;
            default:
                return <Badge bg="secondary">Unknown</Badge>;
        }
    };

    return (
        <React.Fragment>
            <Head title={t('Alert Logs') + ' | Power Dashboard'} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('Alert Logs')} pageTitle={t('Monitoring')} />
                    
                    <Row>
                        <Col lg={12}>
                            <Card>
                                <Card.Header>
                                    <div className="d-flex align-items-center">
                                        <h5 className="card-title mb-0 flex-grow-1">{t('System Alert Logs')}</h5>
                                        <div className="flex-shrink-0">
                                            <Button variant="primary" size="sm" className="me-2">
                                                <FeatherIcon icon="download" className="icon-sm me-1" />
                                                {t('Export')}
                                            </Button>
                                            <Button variant="outline-primary" size="sm">
                                                <FeatherIcon icon="filter" className="icon-sm me-1" />
                                                {t('Filter')}
                                            </Button>
                                        </div>
                                    </div>
                                </Card.Header>
                                <Card.Body>
                                    <div className="row mb-3">
                                        <div className="col-md-6">
                                            <InputGroup>
                                                <InputGroup.Text>
                                                    <FeatherIcon icon="search" className="icon-sm" />
                                                </InputGroup.Text>
                                                <Form.Control placeholder="Search alerts..." />
                                            </InputGroup>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Select>
                                                <option>All Severities</option>
                                                <option>Critical</option>
                                                <option>High</option>
                                                <option>Medium</option>
                                                <option>Low</option>
                                            </Form.Select>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Select>
                                                <option>All Status</option>
                                                <option>Active</option>
                                                <option>Resolved</option>
                                                <option>Acknowledged</option>
                                            </Form.Select>
                                        </div>
                                    </div>
                                    
                                    <div className="table-responsive">
                                        <Table className="table-borderless table-nowrap align-middle mb-0">
                                            <thead className="table-light">
                                                <tr>
                                                    <th scope="col">Device</th>
                                                    <th scope="col">Alert Type</th>
                                                    <th scope="col">Severity</th>
                                                    <th scope="col">Message</th>
                                                    <th scope="col">Location</th>
                                                    <th scope="col">Timestamp</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {alerts.map((alert) => (
                                                    <tr key={alert.id}>
                                                        <td>
                                                            <div className="d-flex align-items-center">
                                                                <div className="flex-shrink-0">
                                                                    <div className="avatar-sm">
                                                                        <div className="avatar-title bg-light rounded">
                                                                            <FeatherIcon icon="alert-triangle" className="icon-sm text-warning" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div className="flex-grow-1 ms-3">
                                                                    <h6 className="mb-0">{alert.device}</h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{alert.type}</td>
                                                        <td>{getSeverityBadge(alert.severity)}</td>
                                                        <td>
                                                            <span className="text-muted">{alert.message}</span>
                                                        </td>
                                                        <td>{alert.location}</td>
                                                        <td>{alert.timestamp}</td>
                                                        <td>{getStatusBadge(alert.status)}</td>
                                                        <td>
                                                            <Dropdown>
                                                                <Dropdown.Toggle variant="light" size="sm">
                                                                    <FeatherIcon icon="more-horizontal" className="icon-sm" />
                                                                </Dropdown.Toggle>
                                                                <Dropdown.Menu>
                                                                    <Dropdown.Item>
                                                                        <FeatherIcon icon="eye" className="icon-sm me-2" />
                                                                        View Details
                                                                    </Dropdown.Item>
                                                                    <Dropdown.Item>
                                                                        <FeatherIcon icon="check" className="icon-sm me-2" />
                                                                        Mark Resolved
                                                                    </Dropdown.Item>
                                                                    <Dropdown.Item>
                                                                        <FeatherIcon icon="bell" className="icon-sm me-2" />
                                                                        Acknowledge
                                                                    </Dropdown.Item>
                                                                </Dropdown.Menu>
                                                            </Dropdown>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </Table>
                                    </div>
                                </Card.Body>
                            </Card>
                        </Col>
                    </Row>
                </Container>
            </div>
        </React.Fragment>
    );
};

AlertLogs.layout = (page: any) => <Layout children={page} />;
export default AlertLogs;
