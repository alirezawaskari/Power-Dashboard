import React from 'react';
import { Card, CardBody, CardHeader, ProgressBar, Row, Col } from 'react-bootstrap';
import CountUp from "react-countup";
import FeatherIcon from "feather-icons-react";
import { useTranslation } from 'react-i18next';

const PerformanceMetrics = () => {
    const { t } = useTranslation();
    // Mock performance data (replace with API call later)
    const performanceData = {
        cpu: 23,
        memory: 67,
        storage: 45,
        network: 12,
        database: 34,
        apiResponse: 150
    };

    const metrics = [
        {
            id: 1,
            title: t("CPU Usage"),
            value: performanceData.cpu,
            icon: "cpu",
            color: "primary",
            unit: "%"
        },
        {
            id: 2,
            title: t("Memory Usage"),
            value: performanceData.memory,
            icon: "hard-drive",
            color: "warning",
            unit: "%"
        },
        {
            id: 3,
            title: t("Storage Usage"),
            value: performanceData.storage,
            icon: "database",
            color: "info",
            unit: "%"
        },
        {
            id: 4,
            title: t("Network Load"),
            value: performanceData.network,
            icon: "wifi",
            color: "success",
            unit: "%"
        }
    ];

    const getPerformanceColor = (value: number) => {
        if (value < 50) return 'success';
        if (value < 80) return 'warning';
        return 'danger';
    };

    return (
        <Card>
            <CardHeader>
                <h4 className="card-title mb-0">{t('Performance Metrics')}</h4>
            </CardHeader>
            <CardBody>
                <Row>
                    {metrics.map((metric) => (
                        <Col md={6} key={metric.id} className="mb-3">
                            <div className="d-flex align-items-center p-3 border rounded">
                                <div className="flex-shrink-0 me-3">
                                    <FeatherIcon 
                                        icon={metric.icon} 
                                        size={24} 
                                        className={`text-${metric.color}`}
                                    />
                                </div>
                                <div className="flex-grow-1">
                                    <h6 className="mb-1">{metric.title}</h6>
                                    <div className="d-flex justify-content-between align-items-center mb-2">
                                        <span className="fw-semibold">
                                            <CountUp
                                                start={0}
                                                end={metric.value}
                                                duration={2}
                                                suffix={metric.unit}
                                            />
                                        </span>
                                        <small className="text-muted">
                                            {metric.value < 50 ? t('Good') : metric.value < 80 ? t('Warning') : t('Critical')}
                                        </small>
                                    </div>
                                    <ProgressBar 
                                        now={metric.value} 
                                        variant={getPerformanceColor(metric.value)}
                                        className="mb-0"
                                    />
                                </div>
                            </div>
                        </Col>
                    ))}
                </Row>

                {/* Additional Metrics */}
                <Row className="mt-3">
                    <Col md={6}>
                        <div className="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <h6 className="mb-1">{t('Database Performance')}</h6>
                                <small className="text-muted">{t('Query response time')}</small>
                            </div>
                            <div className="text-end">
                                <h5 className="mb-0 text-primary">
                                    <CountUp
                                        start={0}
                                        end={performanceData.database}
                                        duration={2}
                                        suffix="ms"
                                    />
                                </h5>
                            </div>
                        </div>
                    </Col>
                    <Col md={6}>
                        <div className="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <h6 className="mb-1">{t('API Response Time')}</h6>
                                <small className="text-muted">{t('Average response')}</small>
                            </div>
                            <div className="text-end">
                                <h5 className="mb-0 text-success">
                                    <CountUp
                                        start={0}
                                        end={performanceData.apiResponse}
                                        duration={2}
                                        suffix="ms"
                                    />
                                </h5>
                            </div>
                        </div>
                    </Col>
                </Row>
            </CardBody>
        </Card>
    );
};

export default PerformanceMetrics;
