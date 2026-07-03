FROM node:20-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm i
COPY . .
RUN npm run build

FROM nginx:alpine
RUN addgroup -g 1001 -S nginxuser && \
    adduser -S -D -H -u 1001 -G nginxuser nginxuser

COPY --from=build --chown=nginxuser:nginxuser /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /run/nginx /var/cache/nginx && \
    chown -R nginxuser:nginxuser /run /var/cache/nginx /var/log/nginx /etc/nginx/conf.d

USER nginxuser
EXPOSE 8080
ENTRYPOINT ["nginx", "-g", "daemon off;"]