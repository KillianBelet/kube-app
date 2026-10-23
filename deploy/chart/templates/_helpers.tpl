{{- define "kube-app.fullname" -}}
{{- .Chart.Name -}}
{{- end -}}

{{- define "kube-app.labels" -}}
app.kubernetes.io/name: {{ .Chart.Name }}
app.kubernetes.io/instance: {{ .Release.Name }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end -}}
