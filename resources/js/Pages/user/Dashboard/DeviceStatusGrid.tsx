import React from 'react';
import { Card, CardBody, CardHeader, Row, Col, Badge, ProgressBar } from 'react-bootstrap';
import CountUp from "react-countup";
import FeatherIcon from "feather-icons-react";
import { useTranslation } from 'react-i18next';

const DeviceStatusGrid = () => {
    const { t } = useTranslation();

    // Mock device data (replace with API call later)
    const devices = [
        {
            id: 1,
            name: t("Main Electrical Panel"),
            status: "online",
            power: 2450,
            voltage: 230,
            current: 10.6,
            efficiency: 94,
            location: t("Basement"),
            lastSeen: t("2 min ago")
        },
        {
            id: 2,
            name: t("HVAC System"),
            status: "online",
            power: 1800,
            voltage: 230,
            current: 7.8,
            efficiency: 87,
            location: t("Roof"),
            lastSeen: t("1 min ago")
        },
        {
            id: 3,
            name: t("Lighting System"),
            status: "offline",
            power: 0,
            voltage: 0,
            current: 0,
            efficiency: 0,
            location: t("All Floors"),
            lastSeen: t("15 min ago")
        },
        {
            id: 4,
            name: t("Server Room"),
            status: "online",
            power: 3200,
            voltage: 230,
            current: 13.9,
            efficiency: 92,
            location: t("2nd Floor"),
            lastSeen: t("30 sec ago")
        },
        {
            id: 5,
            name: t("Kitchen Equipment"),
            status: "maintenance",
            power: 1200,
            voltage: 230,
            current: 5.2,
            efficiency: 78,
            location: t("1st Floor"),
            lastSeen: t("5 min ago")
        },
        {
            id: 6,
            name: t("Security System"),
            status: "online",
            power: 450,
            voltage: 230,
            current: 2.0,
            efficiency: 96,
            location: t("All Floors"),
            lastSeen: t("1 min ago")
        }
    ];

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'online': return 'success';
            case 'offline': return 'danger';
            case 'maintenance': return 'warning';
            default: return 'secondary';
        }
    };

    const getStatusIcon = (status: string) => {
        switch (status) {
            case 'online': return 'wifi';
            case 'offline': return 'wifi-off';
            case 'maintenance': return 'tool';
            default: return 'help-circle';
        }
    };

    const getEfficiencyColor = (efficiency: number) => {
        if (efficiency >= 90) return 'success';
        if (efficiency >= 80) return 'warning';
        return 'danger';
    };

    return (
        <Card>
            <CardHeader>
                <h4 className="card-title mb-0">{t('Device Status Overview')}</h4>
            </CardHeader>
            <CardBody>
                <Row>
                    {devices.map((device) => (
                        <Col lg={4} md={6} key={device.id}>
                            <Card className="card-animate border">
                                <CardBody>
                                    <div className="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 className="mb-1">{device.name}</h6>
                                            <small className="text-muted">{device.location}</small>
                                        </div>
                                        <Badge bg={getStatusColor(device.status)} className="d-flex align-items-center">
                                            <FeatherIcon icon={getStatusIcon(device.status)} size={14} className="me-1" />
                                            {device.status === 'online' ? t('Online') : 
                                             device.status === 'offline' ? t('Offline') : 
                                             device.status === 'maintenance' ? t('Maintenance') : device.status}
                                        </Badge>
                                    </div>

                                    <div className="mb-3">
                                        <div className="d-flex justify-content-between align-items-center mb-2">
                                            <span className="text-muted small">{t('Current Power')}</span>
                                            <span className="fw-semibold">
                                                <CountUp
                                                    start={0}
                                                    end={device.power}
                                                    duration={1}
                                                    separator=","
                                                />
                                                W
                                            </span>
                                        </div>
                                        <div className="d-flex justify-content-between align-items-center mb-2">
                                            <span className="text-muted small">{t('Voltage')}</span>
                                            <span>{device.voltage}V</span>
                                        </div>
                                        <div className="d-flex justify-content-between align-items-center mb-2">
                                            <span className="text-muted small">{t('Current')}</span>
                                            <span>{device.current}A</span>
                                        </div>
                                    </div>

                                    <div className="mb-3">
                                        <div className="d-flex justify-content-between align-items-center mb-1">
                                            <span className="text-muted small">{t('Efficiency')}</span>
                                            <span className={`text-${getEfficiencyColor(device.efficiency)} fw-semibold`}>
                                                {device.efficiency}%
                                            </span>
                                        </div>
                                        <ProgressBar 
                                            now={device.efficiency} 
                                            variant={getEfficiencyColor(device.efficiency)}
                                            className="mb-2"
                                        />
                                    </div>

                                    <div className="d-flex justify-content-between align-items-center">
                                        <small className="text-muted">{t('Last seen')}: {device.lastSeen}</small>
                                        <button className="btn btn-sm btn-outline-primary">
                                            {t('Details')}
                                        </button>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                    ))}
                </Row>
            </CardBody>
        </Card>
    );
};

export default DeviceStatusGrid;
