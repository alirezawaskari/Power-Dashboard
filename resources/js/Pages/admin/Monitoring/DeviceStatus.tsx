import React, { useState } from 'react';
import { Card, Col, Row, Badge, ProgressBar, Table, Button, Dropdown, Container } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import Layout from '../../../Layouts';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import FeatherIcon from 'feather-icons-react';
import { BasicLineCharts } from '../../templates/Charts/ApexCharts/LineCharts/LineCharts';
import { useTranslation } from 'react-i18next';

const DeviceStatus = () => {
    const { t } = useTranslation();
    const [devices, setDevices] = useState([
        {
            id: 1,
            name: 'Power Meter #001',
            location: 'Building A - Floor 1',
            status: 'online',
            powerConsumption: 85,
            voltage: 220,
            current: 15.5,
            lastUpdate: '2 minutes ago',
            uptime: '99.8%'
        },
        {
            id: 2,
            name: 'Power Meter #002',
            location: 'Building A - Floor 2',
            status: 'online',
            powerConsumption: 92,
            voltage: 220,
            current: 18.2,
            lastUpdate: '1 minute ago',
            uptime: '99.9%'
        },
        {
            id: 3,
            name: 'Power Meter #003',
            location: 'Building B - Floor 1',
            status: 'offline',
            powerConsumption: 0,
            voltage: 0,
            current: 0,
            lastUpdate: '15 minutes ago',
            uptime: '95.2%'
        },
        {
            id: 4,
            name: 'Power Meter #004',
            location: 'Building B - Floor 2',
            status: 'warning',
            powerConsumption: 45,
            voltage: 180,
            current: 8.5,
            lastUpdate: '5 minutes ago',
            uptime: '98.7%'
        }
    ]);

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'online':
                return <Badge bg="success">Online</Badge>;
            case 'offline':
                return <Badge bg="danger">Offline</Badge>;
            case 'warning':
                return <Badge bg="warning">Warning</Badge>;
            default:
                return <Badge bg="secondary">Unknown</Badge>;
        }
    };

    const getPowerConsumptionColor = (consumption: number) => {
        if (consumption > 80) return 'danger';
        if (consumption > 60) return 'warning';
        return 'success';
    };

    return (
        <React.Fragment>
            <Head title={t('Device Status') + ' | Power Dashboard'} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('Device Status')} pageTitle={t('Monitoring')} />
                    
                    <Row>
                        <Col lg={8}>
                            <Card>
                                <Card.Header>
                                    <div className="d-flex align-items-center">
                                        <h5 className="card-title mb-0 flex-grow-1">{t('Real-time Device Status')}</h5>
                                        <div className="flex-shrink-0">
                                            <Button variant="primary" size="sm">
                                                <FeatherIcon icon="refresh-cw" className="icon-sm me-1" />
                                                {t('Refresh')}
                                            </Button>
                                        </div>
                                    </div>
                                </Card.Header>
                                <Card.Body>
                                    <div className="table-responsive">
                                        <Table className="table-borderless table-nowrap align-middle mb-0">
                                            <thead className="table-light">
                                                <tr>
                                                    <th scope="col">{t('Device')}</th>
                                                    <th scope="col">{t('Location')}</th>
                                                    <th scope="col">{t('Status')}</th>
                                                    <th scope="col">{t('Power Consumption')}</th>
                                                    <th scope="col">{t('Voltage (V)')}</th>
                                                    <th scope="col">{t('Current (A)')}</th>
                                                    <th scope="col">{t('Uptime')}</th>
                                                    <th scope="col">{t('Last Update')}</th>
                                                    <th scope="col">{t('Actions')}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {devices.map((device) => (
                                                    <tr key={device.id}>
                                                        <td>
                                                            <div className="d-flex align-items-center">
                                                                <div className="flex-shrink-0">
                                                                    <div className="avatar-sm">
                                                                        <div className="avatar-title bg-light rounded">
                                                                            <FeatherIcon icon="zap" className="icon-sm text-primary" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div className="flex-grow-1 ms-3">
                                                                    <h6 className="mb-0">{device.name}</h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{device.location}</td>
                                                        <td>{getStatusBadge(device.status)}</td>
                                                        <td>
                                                            <div className="d-flex align-items-center">
                                                                <div className="flex-grow-1 me-3">
                                                                    <ProgressBar 
                                                                        now={device.powerConsumption} 
                                                                        variant={getPowerConsumptionColor(device.powerConsumption)}
                                                                        style={{ height: '6px' }}
                                                                    />
                                                                </div>
                                                                <span className="fw-medium">{device.powerConsumption}%</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span className={`fw-medium text-${device.voltage < 200 ? 'warning' : 'success'}`}>
                                                                {device.voltage}V
                                                            </span>
                                                        </td>
                                                        <td>{device.current}A</td>
                                                        <td>
                                                            <span className="fw-medium text-success">{device.uptime}</span>
                                                        </td>
                                                        <td>{device.lastUpdate}</td>
                                                        <td>
                                                            <Dropdown>
                                                                <Dropdown.Toggle variant="light" size="sm">
                                                                    <FeatherIcon icon="more-horizontal" className="icon-sm" />
                                                                </Dropdown.Toggle>
                                                                <Dropdown.Menu>
                                                                    <Dropdown.Item>
                                                                        <FeatherIcon icon="eye" className="icon-sm me-2" />
                                                                        {t('View Details')}
                                                                    </Dropdown.Item>
                                                                    <Dropdown.Item>
                                                                        <FeatherIcon icon="settings" className="icon-sm me-2" />
                                                                        {t('Configure')}
                                                                    </Dropdown.Item>
                                                                    <Dropdown.Item>
                                                                        <FeatherIcon icon="download" className="icon-sm me-2" />
                                                                        {t('Export Data')}
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
                        <Col lg={4}>
                            <Card>
                                <Card.Header>
                                    <h5 className="card-title mb-0">{t('Power Consumption Trend')}</h5>
                                </Card.Header>
                                <Card.Body>
                                    <BasicLineCharts dataColors='["--vz-primary"]' />
                                </Card.Body>
                            </Card>
                        </Col>
                    </Row>
                </Container>
            </div>
        </React.Fragment>
    );
};

DeviceStatus.layout = (page: any) => <Layout children={page} />;
export default DeviceStatus;
