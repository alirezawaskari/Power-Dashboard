import React from 'react';
import { Card, CardBody, CardHeader, Row, Col } from 'react-bootstrap';
import CountUp from "react-countup";
import FeatherIcon from "feather-icons-react";
import { useTranslation } from 'react-i18next';

const SystemHealthOverview = () => {
    const { t } = useTranslation();

    // Mock system data (replace with API call later)
    const systemData = {
        totalDevices: 45,
        onlineDevices: 42,
        totalUsers: 12,
        activeAlerts: 8,
        systemHealth: 94,
        uptime: 99.8,
        lastBackup: t("2 hours ago"),
        storageUsed: 67
    };

    const healthMetrics = [
        {
            id: 1,
            title: t("Total Devices"),
            value: systemData.totalDevices,
            icon: "server",
            color: "primary",
            suffix: ""
        },
        {
            id: 2,
            title: t("Online Devices"),
            value: systemData.onlineDevices,
            icon: "wifi",
            color: "success",
            suffix: ""
        },
        {
            id: 3,
            title: t("Total Users"),
            value: systemData.totalUsers,
            icon: "users",
            color: "info",
            suffix: ""
        },
        {
            id: 4,
            title: t("Active Alerts"),
            value: systemData.activeAlerts,
            icon: "alert-triangle",
            color: "warning",
            suffix: ""
        },
        {
            id: 5,
            title: t("System Health"),
            value: systemData.systemHealth,
            icon: "activity",
            color: "success",
            suffix: "%"
        },
        {
            id: 6,
            title: t("Uptime"),
            value: systemData.uptime,
            icon: "clock",
            color: "primary",
            suffix: "%"
        }
    ];

    return (
        <Card>
            <CardHeader>
                <h4 className="card-title mb-0">{t('System Health Overview')}</h4>
            </CardHeader>
            <CardBody>
                <Row>
                    {healthMetrics.map((metric) => (
                        <Col md={6} key={metric.id}>
                            <Card className="card-animate border">
                                <CardBody>
                                    <div className="d-flex justify-content-between">
                                        <div>
                                            <p className="fw-medium text-muted mb-0">{metric.title}</p>
                                            <h2 className="mt-4 ff-secondary fw-semibold">
                                                <CountUp
                                                    start={0}
                                                    end={metric.value}
                                                    duration={2}
                                                    decimals={metric.suffix === "%" ? 1 : 0}
                                                    suffix={metric.suffix}
                                                />
                                            </h2>
                                        </div>
                                        <div>
                                            <div className="avatar-sm flex-shrink-0">
                                                <span className={`avatar-title bg-${metric.color}-subtle rounded-circle fs-2`}>
                                                    <FeatherIcon
                                                        icon={metric.icon}
                                                        className={`text-${metric.color}`}
                                                    />
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                    ))}
                </Row>

                {/* System Status */}
                <Row className="mt-3">
                    <Col md={6}>
                        <div className="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <h6 className="mb-1">{t('Last Backup')}</h6>
                                <small className="text-muted">{systemData.lastBackup}</small>
                            </div>
                            <FeatherIcon icon="database" size={20} className="text-info" />
                        </div>
                    </Col>
                    <Col md={6}>
                        <div className="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <h6 className="mb-1">{t('Storage Used')}</h6>
                                <small className="text-muted">{systemData.storageUsed}%</small>
                            </div>
                            <FeatherIcon icon="hard-drive" size={20} className="text-warning" />
                        </div>
                    </Col>
                </Row>
            </CardBody>
        </Card>
    );
};

export default SystemHealthOverview;
