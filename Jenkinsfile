pipeline{
    agent any
    environment{
        imageName = 'thetiptop'
        registryCredential = 'dockerhub'
        registry = 'docker.io'
        registryUrl = 'https://index.docker.io/v1/'
    }
    stages{
        // checkout code from git
        stage('Checkout'){
            steps{
                deleteDir()
                checkout scm
            }
        }
        // Remove all containers and volumes
        stage('Clean'){
            steps{
                script{
                    sh 'docker rm -f $(docker ps -a -q)'
                    sh 'docker volume rm $(docker volume ls -q)'
                }
            }
        }
        // start docker container with docker compose file
        stage('Start'){
            steps{
                script{
                    sh 'docker-compose up -d'
                }
            }
        }
        // run test cases phpunit
        stage('Test'){
            steps{
                script{
                    // install phpunit-bridge and browser-kit and css-selector
                    sh 'docker exec -it php composer require symfony/phpunit-bridge symfony/browser-kit symfony/css-selector --dev'
                    sh 'docker exec -it php php bin/phpunit --coverage-html=coverage --coverage-clover=coverage.xml'
                    // run test cases phpunit and report couverage and phpunit-report to store test result
                    sh 'docker exec -it php php bin/phpunit --coverage-clover storage/logs/coverage.xml --log-junit storage/logs/phpunit.junit.xml'
                    // make directory for test result
                    sh 'mkdir -p test-results'
                    // copy test result to test-results directory
                    sh 'docker cp php:/var/www/html/storage/logs/phpunit.junit.xml test-results'
                }
            }
            // publish test result
            post{
                always{
                    junit 'test-results/\*.xml'
                }
            }
        }
        // build docker image
        stage('Build'){
            steps{
                script{
                    docker.build(imageName)
                }
            }
        }
        // push docker image to docker hub
        stage('Push'){
            steps{
                script{
                    docker.withRegistry(registryUrl, registryCredential){
                        docker.image(imageName).push()
                    }
                }
            }
        }
    }
}