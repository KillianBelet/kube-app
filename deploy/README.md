# deploy/

Manifests de déploiement de l'application, prêts à être synchronisés par ArgoCD.

## Structure

- `chart/` — chart Helm de l'application (image app + ConfigMap + Service + Ingress)

Port et variables d'environnement repris de `docker-compose.yaml` (app sur le port 80,
connexion MySQL via `DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`).

## Test local

```bash
helm lint deploy/chart
helm template kube-app deploy/chart
```

## À faire (prochains tickets)

- créer le Secret scellé `kube-app-secrets` (clés `APP_KEY`, `DB_PASSWORD`) — voir
  `platform/sealed-secrets/` dans kube-infra pour la procédure
- ajouter la base de données via le chart officiel (dépendance Helm, ou chart séparé)
  et faire correspondre `values.yaml: config.dbHost` au nom du service créé
- repo GitOps kustomize pointant vers ce chart, synchronisé par ArgoCD
