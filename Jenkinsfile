pipeline{
    agent any
    environment{
        IMAGE_NAME = 'ebenbrah/thetiptop'
        LOCAL_IMAGE = 'thetiptop'
        CONTAINER_NAME = 'web_thetiptop'
        registryCredential = 'dockerhubuser'
        SCANNER_HOME = tool 'sonar-scanner'
    }
    
    options{
        buildDiscarder(logRotator(numToKeepStr: '5'))
    }

    stages{
        stage('Checkout'){
            steps{
                deleteDir()
                checkout scm
            }
        }

        stage('Clean'){
            steps{
                script{
                    // sh 'docker compose -f docker-compose.yml down -v'
                    sh 'docker stop ${CONTAINER_NAME} || true && docker rm ${CONTAINER_NAME} || true'
                    sh 'docker system prune -af --volumes'
                }
            }
        }

        stage('Start'){
            steps{
                script{
                    sh 'docker compose up -d'
                }
            }
        }
        
        stage('Test'){
            steps{
                script{
                    sh 'docker exec -t ${CONTAINER_NAME} composer require --dev symfony/test-pack symfony/panther dbrekelmans/bdi --no-interaction --no-progress'
                    sh 'docker exec -t ${CONTAINER_NAME} vendor/bin/simple-phpunit --coverage-html=coverage --coverage-clover=coverage.xml'
                    sh 'docker exec -t ${CONTAINER_NAME} vendor/bin/simple-phpunit --coverage-clover storage/logs/coverage.xml --log-junit storage/logs/phpunit.junit.xml'
                    sh 'mkdir -p test-results'
                    sh 'docker cp ${CONTAINER_NAME}:/var/www/html/thetiptop/storage ${WORKSPACE}'
                }
            }
            post{
                always{
                    junit 'storage/logs/*.xml'
                }
            }
        }

        stage('SonarQube'){
            steps{
                script{
                    withSonarQubeEnv('SonarQube'){
                        sh '${SCANNER_HOME}/bin/sonar-scanner \
                        -D sonar.projectKey=TheTipTop \
                        -D sonar.sources=. \
                        -D sonar.php.coverage.reportPaths=storage/logs/coverage.xml \
                        -D sonar.php.tests.reportPaths=storage/logs/phpunit.junit.xml'
                    }
                }
            }
        }

        stage('Push'){
            steps{
                script{
                   docker.withRegistry('', registryCredential){
                        sh 'docker tag ${LOCAL_IMAGE} ${IMAGE_NAME}:$BUILD_NUMBER'
                        sh 'docker push ${IMAGE_NAME}:$BUILD_NUMBER'
                        sh 'docker tag ${LOCAL_IMAGE} ${IMAGE_NAME}:latest'
                        sh 'docker push ${IMAGE_NAME}:latest'
                    }
                }
            }
            post{
                always{
                    sh 'docker logout'
                }
            }
        }

        stage('Deploy'){
            steps{
                /* script{
                    sshagent(['ssh-key']){
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@prod "docker pull ebenbrah/thetiptop:latest"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@prod "docker stop thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@prod "docker rm thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@prod "docker run -d -p 80:80 --name thetiptop thetiptop"'
                    }
                } */
                echo 'Deploy prod'
            }
        }

        stage('Check'){
            steps{
                /* script{
                    sshagent(['ssh-key']){
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@prod "docker ps"'
                    }
                } */
                echo 'Check'
            }
        }
    }
}