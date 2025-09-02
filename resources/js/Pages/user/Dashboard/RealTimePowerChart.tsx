import React, { useState, useEffect } from 'react';
import { Card, CardBody, CardHeader, Button, ButtonGroup, Modal } from 'react-bootstrap';
import ReactApexChart from "react-apexcharts";
import getChartColorsArray from "../../../Components/Common/ChartsDynamicColor";
import { useTranslation } from 'react-i18next';
import FullCalendar from "@fullcalendar/react";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import BootstrapTheme from "@fullcalendar/bootstrap";

const RealTimePowerChart = () => {
    const { t } = useTranslation();
    const [timeRange, setTimeRange] = useState('1h');
    const [powerData, setPowerData] = useState([]);
    const [showCalendar, setShowCalendar] = useState(false);
    const [selectedDate, setSelectedDate] = useState('');

    const handleDateSelect = (selectInfo: any) => {
        setSelectedDate(selectInfo.startStr);
        setShowCalendar(false);
    };

    // Mock real-time data (replace with WebSocket later)
    useEffect(() => {
        const generateMockData = () => {
            const now = new Date();
            const data = [];
            for (let i = 0; i < 60; i++) {
                const time = new Date(now.getTime() - (60 - i) * 60000);
                data.push({
                    x: time.getTime(),
                    y: Math.floor(Math.random() * 500) + 2000 // 2000-2500W range
                });
            }
            return data;
        };

        setPowerData(generateMockData());
        
        // Simulate real-time updates
        const interval = setInterval(() => {
            setPowerData(prev => {
                const newData = [...prev];
                newData.shift();
                newData.push({
                    x: new Date().getTime(),
                    y: Math.floor(Math.random() * 500) + 2000
                });
                return newData;
            });
        }, 60000); // Update every minute

        return () => clearInterval(interval);
    }, []);

    const chartColors = getChartColorsArray('["--vz-success"]');

    const options = {
        chart: {
            type: 'line',
            height: 350,
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
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
            text: t('Real-Time Power Consumption'),
            align: 'left',
            style: {
                fontWeight: 500,
                fontSize: '16px'
            },
        },
        subtitle: {
            text: t('Live monitoring of electrical consumption'),
            align: 'left',
            style: {
                fontSize: '12px'
            }
        },
        xaxis: {
            type: 'datetime',
            labels: {
                datetimeFormatter: {
                    year: 'yyyy',
                    month: 'MMM \'yy',
                    day: 'dd MMM',
                    hour: 'HH:mm'
                }
            }
        },
        yaxis: {
            title: {
                text: t('Power Consumption (W)')
            },
            labels: {
                formatter: function (val: number) {
                    return val.toFixed(0) + 'W';
                }
            }
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
            y: {
                formatter: function (val: number) {
                    return val.toFixed(0) + 'W';
                }
            }
        },
        colors: chartColors,
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
        },
        annotations: {
            yaxis: [
                {
                    y: 2300,
                    borderColor: '#dc3545',
                    borderWidth: 2,
                    strokeDashArray: 5,
                    label: {
                        borderColor: '#dc3545',
                        style: {
                            color: '#fff',
                            background: '#dc3545',
                        },
                        text: t('Critical Threshold'),
                    }
                }
            ]
        }
    };

    const series = [{
        name: t('Power Consumption'),
        data: powerData
    }];

    return (
        <Card>
            <CardHeader className="d-flex justify-content-between align-items-center">
                <h4 className="card-title mb-0">{t('Real-Time Power Monitoring')}</h4>
                <div className="d-flex align-items-center">
                    <Button variant="outline-secondary" size="sm" className="me-2" onClick={() => setShowCalendar(true)}>
                        <i className="ri-calendar-line"></i>
                        {selectedDate && <span className="ms-1">{selectedDate}</span>}
                    </Button>
                    <ButtonGroup size="sm">
                        <Button 
                            variant={timeRange === '1h' ? 'primary' : 'outline-primary'}
                            onClick={() => setTimeRange('1h')}
                        >
                            {t('1H')}
                        </Button>
                        <Button 
                            variant={timeRange === '6h' ? 'primary' : 'outline-primary'}
                            onClick={() => setTimeRange('6h')}
                        >
                            {t('6H')}
                        </Button>
                        <Button 
                            variant={timeRange === '24h' ? 'primary' : 'outline-primary'}
                            onClick={() => setTimeRange('24h')}
                        >
                            {t('24H')}
                        </Button>
                    </ButtonGroup>
                </div>
            </CardHeader>
            <CardBody>
                <ReactApexChart
                    options={options}
                    series={series}
                    type="area"
                    height={350}
                    className="apex-charts"
                />
            </CardBody>

            <Modal show={showCalendar} onHide={() => setShowCalendar(false)} size="lg">
                <Modal.Header closeButton>
                    <Modal.Title>{t('Select Date')}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <FullCalendar
                        plugins={[dayGridPlugin, interactionPlugin, BootstrapTheme]}
                        initialView="dayGridMonth"
                        height="auto"
                        selectable={true}
                        selectMirror={true}
                        dayMaxEvents={true}
                        weekends={true}
                        events={[]}
                        select={handleDateSelect}
                    />
                </Modal.Body>
            </Modal>
        </Card>
    );
};

export default RealTimePowerChart;
