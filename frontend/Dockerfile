FROM node:16.19.0-alpine as build

WORKDIR /code

COPY . .

RUN rm -rf node_modules && \
    npm install && \
    npm run build

FROM node:16.19.0-alpine as base

COPY --from=build /code/dist /var/www/html

WORKDIR /var/www/html

RUN npm i browser-sync@2.29.3

# Clean up image
RUN rm -rf /tmp/* /var/cache

CMD ["npx", "browser-sync", "start", "--server"]
