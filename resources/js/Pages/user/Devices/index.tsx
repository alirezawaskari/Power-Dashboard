import React, { useState } from 'react';
import { Card, CardBody, CardHeader, Row, Col, Button, Badge, Form, InputGroup, Alert, ProgressBar } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import Layout from "../../../Layouts";
import { useTranslation } from 'react-i18next';

const Devices = () => {
    const { t } = useTranslation();
    const [devices] = useState([
        {
            id: 'PM-001',
            name: t('Power Monitor Main Building'),
            type: t('Power Meter'),
            status: t('Online'),
            location: t('Main Building - Floor 1'),
            power: 2450,
            voltage: 230,
            current: 10.7,
            efficiency: 92,
            lastUpdate: t('2 min ago'),
            uptime: '99.8%'
        },
        {
            id: 'PM-002',
            name: t('Power Monitor Lab A'),
            type: t('Power Meter'),
            status: t('Online'),
            location: t('Laboratory A - Floor 2'),
            power: 1800,
            voltage: 230,
            current: 7.8,
            efficiency: 89,
            lastUpdate: t('1 min ago'),
            uptime: '99.5%'
        },
        {
            id: 'PM-003',
            name: t('Power Monitor Server Room'),
            type: t('Power Meter'),
            status: t('Offline'),
            location: t('Server Room - Basement'),
            power: 0,
            voltage: 0,
            current: 0,
            efficiency: 0,
            lastUpdate: t('15 min ago'),
            uptime: '95.2%'
        },
        {
            id: 'PM-004',
            name: t('Power Monitor Workshop'),
            type: t('Power Meter'),
            status: t('Online'),
            location: t('Workshop - Floor 1'),
            power: 3200,
            voltage: 230,
            current: 13.9,
            efficiency: 94,
            lastUpdate: t('30 sec ago'),
            uptime: '99.9%'
        }
    ]);

    const [showAddDevice, setShowAddDevice] = useState(false);

    const getStatusBadge = (status: string) => {
        const variants: { [key: string]: string } = {
            [t('Online')]: 'success',
            [t('Offline')]: 'danger',
            [t('Warning')]: 'warning',
            [t('Maintenance')]: 'info'
        };
        return <Badge bg={variants[status] || 'secondary'}>{status}</Badge>;
    };

    const getEfficiencyColor = (efficiency: number) => {
        if (efficiency >= 90) return 'success';
        if (efficiency >= 80) return 'warning';
        return 'danger';
    };

    return (
        <React.Fragment>
            <Head title={t('Devices') + ' | ' + t('power_dashboard.pages.dashboard.title')} />
            <div className="page-content">
                <div className="container-fluid">
                    <BreadCrumb title={t('Devices')} pageTitle={t('Dashboard')} />
                    
                    <Row>
                        <Col lg={12}>
                            <Card>
                                <CardHeader>
                                    <div className="d-flex align-items-center justify-content-between">
                                        <h4 className="card-title mb-0">{t('Devices')}</h4>
                                        <Button variant="primary" size="sm" onClick={() => setShowAddDevice(true)}>
                                            <i className="ri-add-line align-middle me-1"></i>
                                            {t('Add Device')}
                                        </Button>
                                    </div>
                                </CardHeader>
                                <CardBody>
                                    <div className="table-responsive">
                                        <table className="table table-nowrap align-middle">
                                            <thead className="table-light">
                                                <tr>
                                                    <th>{t('Device ID')}</th>
                                                    <th>{t('Name')}</th>
                                                    <th>{t('Type')}</th>
                                                    <th>{t('Status')}</th>
                                                    <th>{t('Location')}</th>
                                                    <th>{t('Power (W)')}</th>
                                                    <th>{t('Voltage (V)')}</th>
                                                    <th>{t('Current (A)')}</th>
                                                    <th>{t('Efficiency')}</th>
                                                    <th>{t('Last Update')}</th>
                                                    <th>{t('Uptime')}</th>
                                                    <th>{t('Action')}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {devices.map((device, index) => (
                                                    <tr key={index}>
                                                        <td>
                                                            <span className="fw-medium">{device.id}</span>
                                                        </td>
                                                        <td>{device.name}</td>
                                                        <td>{device.type}</td>
                                                        <td>{getStatusBadge(device.status)}</td>
                                                        <td>{device.location}</td>
                                                        <td>
                                                            <span className="fw-medium">{device.power.toLocaleString()}</span>
                                                        </td>
                                                        <td>{device.voltage}</td>
                                                        <td>{device.current}</td>
                                                        <td>
                                                            <div className="d-flex align-items-center">
                                                                <ProgressBar 
                                                                    now={device.efficiency} 
                                                                    variant={getEfficiencyColor(device.efficiency)}
                                                                    className="me-2"
                                                                    style={{ width: '60px', height: '6px' }}
                                                                />
                                                                <span className="fw-medium">{device.efficiency}%</span>
                                                            </div>
                                                        </td>
                                                        <td>{device.lastUpdate}</td>
                                                        <td>{device.uptime}</td>
                                                        <td>
                                                            <div className="dropdown">
                                                                <Button variant="outline-secondary" size="sm" className="dropdown-toggle" data-bs-toggle="dropdown">
                                                                    {t('Actions')}
                                                                </Button>
                                                                <ul className="dropdown-menu">
                                                                    <li><a className="dropdown-item" href="#"><i className="ri-eye-line me-2"></i>{t('View Details')}</a></li>
                                                                    <li><a className="dropdown-item" href="#"><i className="ri-settings-3-line me-2"></i>{t('Configure')}</a></li>
                                                                    <li><a className="dropdown-item" href="#"><i className="ri-refresh-line me-2"></i>{t('Restart')}</a></li>
                                                                    <li><hr className="dropdown-divider" /></li>
                                                                    <li><a className="dropdown-item text-danger" href="#"><i className="ri-delete-bin-line me-2"></i>{t('Remove')}</a></li>
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
                        <Col lg={4}>
                            <Card>
                                <CardHeader>
                                    <h5 className="card-title mb-0">{t('Device Statistics')}</h5>
                                </CardHeader>
                                <CardBody>
                                    <div className="d-flex justify-content-between mb-3">
                                        <div>
                                            <h6 className="mb-1">{t('Total Devices')}</h6>
                                            <p className="text-muted mb-0">{devices.length}</p>
                                        </div>
                                        <div className="text-end">
                                            <h6 className="mb-1">{t('Online')}</h6>
                                            <p className="text-success mb-0">{devices.filter(d => d.status === t('Online')).length}</p>
                                        </div>
                                    </div>
                                    <div className="d-flex justify-content-between mb-3">
                                        <div>
                                            <h6 className="mb-1">{t('Total Power')}</h6>
                                            <p className="text-muted mb-0">{devices.reduce((sum, d) => sum + d.power, 0).toLocaleString()} W</p>
                                        </div>
                                        <div className="text-end">
                                            <h6 className="mb-1">{t('Avg Efficiency')}</h6>
                                            <p className="text-info mb-0">{Math.round(devices.reduce((sum, d) => sum + d.efficiency, 0) / devices.length)}%</p>
                                        </div>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={8}>
                            <Card>
                                <CardHeader>
                                    <h5 className="card-title mb-0">{t('Quick Actions')}</h5>
                                </CardHeader>
                                <CardBody>
                                    <Row>
                                        <Col md={6}>
                                            <Button variant="outline-primary" className="w-100 mb-2">
                                                <i className="ri-download-line me-2"></i>
                                                {t('Export Device Data')}
                                            </Button>
                                        </Col>
                                        <Col md={6}>
                                            <Button variant="outline-success" className="w-100 mb-2">
                                                <i className="ri-refresh-line me-2"></i>
                                                {t('Refresh All Devices')}
                                            </Button>
                                        </Col>
                                        <Col md={6}>
                                            <Button variant="outline-warning" className="w-100 mb-2">
                                                <i className="ri-settings-3-line me-2"></i>
                                                {t('Bulk Configuration')}
                                            </Button>
                                        </Col>
                                        <Col md={6}>
                                            <Button variant="outline-info" className="w-100 mb-2">
                                                <i className="ri-file-chart-line me-2"></i>
                                                {t('Generate Report')}
                                            </Button>
                                        </Col>
                                    </Row>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>

                    {showAddDevice && (
                        <Row>
                            <Col lg={12}>
                                <Card>
                                    <CardHeader>
                                        <h5 className="card-title mb-0">{t('Add New Device')}</h5>
                                    </CardHeader>
                                    <CardBody>
                                        <Form>
                                            <Row>
                                                <Col md={6}>
                                                    <Form.Group className="mb-3">
                                                        <Form.Label>{t('Device Name')}</Form.Label>
                                                        <Form.Control type="text" placeholder={t('Enter device name')} />
                                                    </Form.Group>
                                                </Col>
                                                <Col md={6}>
                                                    <Form.Group className="mb-3">
                                                        <Form.Label>{t('Device Type')}</Form.Label>
                                                        <Form.Select>
                                                            <option>{t('Power Meter')}</option>
                                                            <option>{t('Energy Monitor')}</option>
                                                            <option>{t('Smart Plug')}</option>
                                                            <option>{t('Circuit Breaker')}</option>
                                                        </Form.Select>
                                                    </Form.Group>
                                                </Col>
                                                <Col md={6}>
                                                    <Form.Group className="mb-3">
                                                        <Form.Label>{t('Location')}</Form.Label>
                                                        <Form.Control type="text" placeholder={t('Enter device location')} />
                                                    </Form.Group>
                                                </Col>
                                                <Col md={6}>
                                                    <Form.Group className="mb-3">
                                                        <Form.Label>{t('IP Address')}</Form.Label>
                                                        <Form.Control type="text" placeholder="192.168.1.100" />
                                                    </Form.Group>
                                                </Col>
                                                <Col md={12}>
                                                    <Form.Group className="mb-3">
                                                        <Form.Label>{t('Description')}</Form.Label>
                                                        <Form.Control as="textarea" rows={3} placeholder={t('Enter device description')} />
                                                    </Form.Group>
                                                </Col>
                                            </Row>
                                            <div className="d-flex gap-2">
                                                <Button variant="primary" type="submit">
                                                    {t('Add Device')}
                                                </Button>
                                                <Button variant="light" onClick={() => setShowAddDevice(false)}>
                                                    {t('Cancel')}
                                                </Button>
                                            </div>
                                        </Form>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    )}
                </div>
            </div>
        </React.Fragment>
    );
};

Devices.layout = (page: any) => <Layout children={page} />
export default Devices;
