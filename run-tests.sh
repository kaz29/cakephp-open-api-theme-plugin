#!/bin/bash

# コンテナをビルド
docker-compose -f docker-compose.test.yml build

# PHP 8.4でテスト実行
echo "Running tests with PHP 8.4..."
docker-compose -f docker-compose.test.yml run --rm test-php84 composer install
docker-compose -f docker-compose.test.yml run --rm test-php84

# PHP 8.3でテスト実行
echo "Running tests with PHP 8.3..."
docker-compose -f docker-compose.test.yml run --rm test-php83 composer install
docker-compose -f docker-compose.test.yml run --rm test-php83

# コンテナを停止・削除
docker-compose -f docker-compose.test.yml down 