import React from 'react';
import { Card, CardBody, CardHeader } from 'react-bootstrap';
import ReactApexChart from "react-apexcharts";
import getChartColorsArray from "../../../Components/Common/ChartsDynamicColor";
import { useTranslation } from 'react-i18next';

const SystemAnalytics = () => {
    const { t } = useTranslation();
    const chartColors = getChartColorsArray('["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger"]');

    // Mock analytics data (replace with API call later)
    const systemData = {
        powerConsumption: [2450, 2480, 2520, 2490, 2510, 2480, 2500, 2530, 2490, 2510, 2480, 2500],
        deviceEfficiency: [94, 87, 92, 78, 96, 89, 91, 85, 93, 88, 90, 92],
        alertFrequency: [2, 1, 3, 0, 2, 1, 4, 2, 1, 3, 2, 1],
        systemLoad: [23, 25, 28, 22, 26, 24, 29, 27, 25, 28, 26, 24]
    };

    const timeLabels = [
        "00:00", "02:00", "04:00", "06:00", "08:00", "10:00", 
        "12:00", "14:00", "16:00", "18:00", "20:00", "22:00"
    ];

    const powerOptions = {
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        title: {
            text: t('System Power Consumption (24h)'),
            align: 'left',
            style: {
                fontWeight: 500,
                fontSize: '14px'
            }
        },
        xaxis: {
            categories: timeLabels
        },
        yaxis: {
            title: {
                text: t('Power (W)')
            }
        },
        colors: [chartColors[0]],
        grid: {
            borderColor: '#f1f1f1',
        }
    };

    const efficiencyOptions = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        title: {
            text: t('Device Efficiency Overview'),
            align: 'left',
            style: {
                fontWeight: 500,
                fontSize: '14px'
            }
        },
        xaxis: {
            categories: timeLabels
        },
        yaxis: {
            title: {
                text: t('Efficiency (%)')
            }
        },
        colors: [chartColors[1]],
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
            }
        }
    };

    const alertOptions = {
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        title: {
            text: t('Alert Frequency Analysis'),
            align: 'left',
            style: {
                fontWeight: 500,
                fontSize: '14px'
            }
        },
        xaxis: {
            categories: timeLabels
        },
        yaxis: {
            title: {
                text: t('Alerts')
            }
        },
        colors: [chartColors[2]],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        grid: {
            borderColor: '#f1f1f1',
        }
    };

    const loadOptions = {
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        title: {
            text: t('System Load Performance'),
            align: 'left',
            style: {
                fontWeight: 500,
                fontSize: '14px'
            }
        },
        xaxis: {
            categories: timeLabels
        },
        yaxis: {
            title: {
                text: t('Load (%)')
            }
        },
        colors: [chartColors[3]],
        grid: {
            borderColor: '#f1f1f1',
        }
    };

    const powerSeries = [{
        name: t('Power Consumption'),
        data: systemData.powerConsumption
    }];

    const efficiencySeries = [{
        name: t('Device Efficiency'),
        data: systemData.deviceEfficiency
    }];

    const alertSeries = [{
        name: t('Alert Frequency'),
        data: systemData.alertFrequency
    }];

    const loadSeries = [{
        name: t('System Load'),
        data: systemData.systemLoad
    }];

    return (
        <Card>
            <CardHeader>
                <h4 className="card-title mb-0">{t('System Analytics')}</h4>
            </CardHeader>
            <CardBody>
                <div className="row">
                    <div className="col-lg-6">
                        <ReactApexChart
                            options={powerOptions}
                            series={powerSeries}
                            type="line"
                            height={350}
                            className="apex-charts"
                        />
                    </div>
                    <div className="col-lg-6">
                        <ReactApexChart
                            options={efficiencyOptions}
                            series={efficiencySeries}
                            type="bar"
                            height={350}
                            className="apex-charts"
                        />
                    </div>
                </div>
                <div className="row mt-4">
                    <div className="col-lg-6">
                        <ReactApexChart
                            options={alertOptions}
                            series={alertSeries}
                            type="area"
                            height={350}
                            className="apex-charts"
                        />
                    </div>
                    <div className="col-lg-6">
                        <ReactApexChart
                            options={loadOptions}
                            series={loadSeries}
                            type="line"
                            height={350}
                            className="apex-charts"
                        />
                    </div>
                </div>
            </CardBody>
        </Card>
    );
};

export default SystemAnalytics;
