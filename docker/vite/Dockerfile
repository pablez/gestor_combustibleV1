FROM node:22

# Create app directory
WORKDIR /var/www/html

# Copy package files first to leverage Docker layer cache
COPY package*.json ./

# Install dependencies (including dev deps for development)
RUN npm ci

# Copy the rest of the application
COPY . .

# Ensure node_modules ownership is set to a non-root user (use 1337 to match sail)
RUN chown -R 1337:1000 /var/www/html/node_modules || true

EXPOSE 5173

# Default command to run the dev server listening on all interfaces
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
