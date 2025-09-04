FROM nginx:alpine

# Copy custom nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/conf.d/default.conf

# Create necessary directories
RUN mkdir -p /var/www/html/storage

EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
