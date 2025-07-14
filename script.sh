#!/bin/bash

set -e

BASE_DIR="infra/k8s"
mkdir -p "$BASE_DIR"

declare -A files=(
  ["namespace.yaml"]="apiVersion: v1
kind: Namespace
metadata:
  name: power-dashboard
"
  ["postgres-deployment.yaml"]="apiVersion: apps/v1
kind: Deployment
metadata:
  name: postgres
  namespace: power-dashboard
spec:
  replicas: 1
  selector:
    matchLabels:
      app: postgres
  template:
    metadata:
      labels:
        app: postgres
    spec:
      containers:
      - name: postgres
        image: postgres:15
        ports:
        - containerPort: 5432
        env:
        - name: POSTGRES_USER
          value: \"admin\"
        - name: POSTGRES_PASSWORD
          value: \"password\"
        - name: POSTGRES_DB
          value: \"powerdb\"
        volumeMounts:
        - mountPath: /var/lib/postgresql/data
          name: pgdata
      volumes:
      - name: pgdata
        persistentVolumeClaim:
          claimName: pgdata-pvc
---
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: pgdata-pvc
  namespace: power-dashboard
spec:
  accessModes:
    - ReadWriteOnce
  resources:
    requests:
      storage: 10Gi
---
apiVersion: v1
kind: Service
metadata:
  name: postgres
  namespace: power-dashboard
spec:
  type: ClusterIP
  ports:
  - port: 5432
    targetPort: 5432
  selector:
    app: postgres
"
  ["frontend-deployment.yaml"]="apiVersion: apps/v1
kind: Deployment
metadata:
  name: frontend
  namespace: power-dashboard
spec:
  replicas: 2
  selector:
    matchLabels:
      app: frontend
  template:
    metadata:
      labels:
        app: frontend
    spec:
      containers:
      - name: frontend
        image: your-frontend-image:latest
        ports:
        - containerPort: 3000
---
apiVersion: v1
kind: Service
metadata:
  name: frontend
  namespace: power-dashboard
spec:
  type: LoadBalancer
  ports:
  - port: 3000
    targetPort: 3000
  selector:
    app: frontend
"
  ["backend-deployment.yaml"]="apiVersion: apps/v1
kind: Deployment
metadata:
  name: backend
  namespace: power-dashboard
spec:
  replicas: 2
  selector:
    matchLabels:
      app: backend
  template:
    metadata:
      labels:
        app: backend
    spec:
      containers:
      - name: backend
        image: your-backend-image:latest
        ports:
        - containerPort: 4000
---
apiVersion: v1
kind: Service
metadata:
  name: backend
  namespace: power-dashboard
spec:
  type: ClusterIP
  ports:
  - port: 4000
    targetPort: 4000
  selector:
    app: backend
"
  ["cms-deployment.yaml"]="apiVersion: apps/v1
kind: Deployment
metadata:
  name: cms
  namespace: power-dashboard
spec:
  replicas: 1
  selector:
    matchLabels:
      app: cms
  template:
    metadata:
      labels:
        app: cms
    spec:
      containers:
      - name: cms
        image: your-cms-image:latest
        ports:
        - containerPort: 1337
---
apiVersion: v1
kind: Service
metadata:
  name: cms
  namespace: power-dashboard
spec:
  type: ClusterIP
  ports:
  - port: 1337
    targetPort: 1337
  selector:
    app: cms
"
  ["pgadmin-deployment.yaml"]="apiVersion: apps/v1
kind: Deployment
metadata:
  name: pgadmin
  namespace: power-dashboard
spec:
  replicas: 1
  selector:
    matchLabels:
      app: pgadmin
  template:
    metadata:
      labels:
        app: pgadmin
    spec:
      containers:
      - name: pgadmin
        image: dpage/pgadmin4
        env:
        - name: PGADMIN_DEFAULT_EMAIL
          value: admin@example.com
        - name: PGADMIN_DEFAULT_PASSWORD
          value: admin
        ports:
        - containerPort: 80
---
apiVersion: v1
kind: Service
metadata:
  name: pgadmin
  namespace: power-dashboard
spec:
  type: LoadBalancer
  ports:
  - port: 80
    targetPort: 80
  selector:
    app: pgadmin
"
  ["prometheus-deployment.yaml"]="apiVersion: apps/v1
kind: Deployment
metadata:
  name: prometheus
  namespace: power-dashboard
spec:
  replicas: 1
  selector:
    matchLabels:
      app: prometheus
  template:
    metadata:
      labels:
        app: prometheus
    spec:
      containers:
      - name: prometheus
        image: prom/prometheus
        ports:
        - containerPort: 9090
        volumeMounts:
        - name: config-volume
          mountPath: /etc/prometheus/
      volumes:
      - name: config-volume
        configMap:
          name: prometheus-config
---
apiVersion: v1
kind: ConfigMap
metadata:
  name: prometheus-config
  namespace: power-dashboard
data:
  prometheus.yml: |
    global:
      scrape_interval: 15s
    scrape_configs:
      - job_name: 'kubernetes-apiservers'
        kubernetes_sd_configs:
          - role: endpoints
      # Add your scrape configs here
---
apiVersion: v1
kind: Service
metadata:
  name: prometheus
  namespace: power-dashboard
spec:
  type: ClusterIP
  ports:
  - port: 9090
    targetPort: 9090
  selector:
    app: prometheus
"
  ["grafana-deployment.yaml"]="apiVersion: apps/v1
kind: Deployment
metadata:
  name: grafana
  namespace: power-dashboard
spec:
  replicas: 1
  selector:
    matchLabels:
      app: grafana
  template:
    metadata:
      labels:
        app: grafana
    spec:
      containers:
      - name: grafana
        image: grafana/grafana
        ports:
        - containerPort: 3000
---
apiVersion: v1
kind: Service
metadata:
  name: grafana
  namespace: power-dashboard
spec:
  type: LoadBalancer
  ports:
  - port: 3000
    targetPort: 3000
  selector:
    app: grafana
"
)

for file in "${!files[@]}"; do
  echo "Writing $BASE_DIR/$file"
  echo "${files[$file]}" > "$BASE_DIR/$file"
done

echo "All Kubernetes manifests generated successfully."
