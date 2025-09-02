import React from 'react';
import { Card, CardBody, CardHeader, ProgressBar, Row, Col } from 'react-bootstrap';
import CountUp from "react-countup";
import FeatherIcon from "feather-icons-react";
import { useTranslation } from 'react-i18next';

const EnergyEfficiency = () => {
    const { t } = useTranslation();
    // Mock efficiency data (replace with API call later)
    const efficiencyData = {
        overall: 89,
        powerFactor: 0.95,
        peakDemand: 3200,
        averageDemand: 2800,
        savings: 12.5
    };

    const getEfficiencyColor = (value: number) => {
        if (value >= 90) return 'success';
        if (value >= 80) return 'warning';
        return 'danger';
    };

    return (
        <Card>
            <CardHeader>
                <h5 className="card-title mb-0">{t('Energy Efficiency')}</h5>
            </CardHeader>
            <CardBody>
                {/* Overall Efficiency */}
                <div className="text-center mb-4">
                    <div className="d-flex justify-content-center align-items-center mb-2">
                        <FeatherIcon 
                            icon="zap" 
                            size={24} 
                            className={`text-${getEfficiencyColor(efficiencyData.overall)} me-2`}
                        />
                        <h3 className="mb-0">
                            <CountUp
                                start={0}
                                end={efficiencyData.overall}
                                duration={2}
                                suffix="%"
                            />
                        </h3>
                    </div>
                    <p className="text-muted mb-0">{t('Overall System Efficiency')}</p>
                </div>

                {/* Efficiency Metrics */}
                <div className="mb-3">
                    <div className="d-flex justify-content-between align-items-center mb-2">
                        <span className="text-muted small">{t('Power Factor')}</span>
                        <span className="fw-semibold">{efficiencyData.powerFactor}</span>
                    </div>
                    <ProgressBar 
                        now={efficiencyData.powerFactor * 100} 
                        variant="info"
                        className="mb-3"
                    />
                </div>

                <div className="mb-3">
                    <div className="d-flex justify-content-between align-items-center mb-2">
                        <span className="text-muted small">{t('Peak Demand')}</span>
                        <span className="fw-semibold">{efficiencyData.peakDemand}W</span>
                    </div>
                    <ProgressBar 
                        now={(efficiencyData.peakDemand / 4000) * 100} 
                        variant="warning"
                        className="mb-3"
                    />
                </div>

                <div className="mb-3">
                    <div className="d-flex justify-content-between align-items-center mb-2">
                        <span className="text-muted small">{t('Average Demand')}</span>
                        <span className="fw-semibold">{efficiencyData.averageDemand}W</span>
                    </div>
                    <ProgressBar 
                        now={(efficiencyData.averageDemand / 4000) * 100} 
                        variant="success"
                        className="mb-3"
                    />
                </div>

                {/* Energy Savings */}
                <div className="text-center p-3 bg-light rounded">
                    <FeatherIcon icon="trending-up" size={20} className="text-success mb-2" />
                    <h6 className="mb-1">
                        <CountUp
                            start={0}
                            end={efficiencyData.savings}
                            duration={2}
                            decimals={1}
                            suffix="%"
                        />
                    </h6>
                    <p className="text-muted mb-0 small">{t('Energy Savings')}</p>
                </div>
            </CardBody>
        </Card>
    );
};

export default EnergyEfficiency;
