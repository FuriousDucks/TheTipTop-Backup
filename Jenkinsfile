pipeline{
    agent any
    environment{
        IMAGE_NAME = 'ebenbrah/thetiptop'
        PREPROD_IMAGE_NAME = 'ebenbrah/preprodthetiptop'
        LOCAL_IMAGE = 'thetiptop'
        PREPROD_LOCAL_IMAGE = 'preprod_thetiptop'
        CONTAINER_NAME = 'web_thetiptop'
        PREPROD_CONTAINER_NAME = 'preprod_web_thetiptop'
        registryCredential = 'dockerhubuser'
        SCANNER_HOME = tool 'sonar-scanner'
        SONNAR_TOKEN = credentials('sonar-token')
        SONNAR_URL = 'https://sonarqube.dsp-archiwebf22-eb-we-fh.fr'
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

            post{
                failure{
                    mail to: 'benbrahim.elmahdi@gmail.com',
                    subject: 'TheTipTop - Checkout Failed',
                    body: 'TheTipTop - Checkout Failed - ${BUILD_URL} - ${BUILD_NUMBER} - ${JOB_NAME} - ${GIT_COMMIT} - ${GIT_BRANCH}'
                }
            }
        }

        stage('Clean'){
            steps{
                script{
                    sh 'docker stop ${CONTAINER_NAME} && docker rm ${CONTAINER_NAME} || true'
                    sh 'docker rmi ${LOCAL_IMAGE} || true'
                    // sh 'docker system prune -af --volumes'
                }
            }
            post{
                failure{
                    mail to: 'benbrahim.elmahdi@gmail.com',
                    subject: 'TheTipTop - Clean Failed',
                    body: 'TheTipTop - Clean Failed - ${BUILD_URL} - ${BUILD_NUMBER} - ${JOB_NAME} - ${GIT_COMMIT} - ${GIT_BRANCH}'
                }
            }
        }

        stage('Deploy Staging'){
            steps{
                script{
                    sh 'docker compose -f docker-compose.yml up -d'
                }
            }
            post{
                failure{
                    mail to: 'benbrahim.elmahdi@gmail.com',
                    subject: 'TheTipTop - Deploy Staging Failed',
                    body: 'TheTipTop - Deploy Staging Failed - ${BUILD_URL} - ${BUILD_NUMBER} - ${JOB_NAME} - ${GIT_COMMIT} - ${GIT_BRANCH}'
                }
            }
        }
        
        stage('Test'){
            steps{
                script{
                    sh 'docker exec -t ${CONTAINER_NAME} composer require --dev symfony/test-pack symfony/browser-kit symfony/css-selector -n'
                    sh 'docker exec -t ${CONTAINER_NAME} yarn install && yarn build -n'
                    // sh 'docker exec -t ${CONTAINER_NAME} vendor/bin/simple-phpunit --coverage-clover storage/logs/coverage.xml --log-junit storage/logs/phpunit.junit.xml'
                    sh 'mkdir -p storage'
                    sh 'docker cp ${CONTAINER_NAME}:/var/www/html/thetiptop/storage ${WORKSPACE}'
                }
            }
            post{
                always{
                    junit 'storage/logs/*.xml'
                }
                failure{
                    mail to: 'benbrahim.elmahdi@gmail.com',
                    subject: 'TheTipTop - Test Failed',
                    body: 'TheTipTop - Test Failed - ${BUILD_URL} - ${BUILD_NUMBER} - ${JOB_NAME} - ${GIT_COMMIT} - ${GIT_BRANCH}'
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
                        -D sonar.php.tests.reportPaths=storage/logs/phpunit.junit.xml \
                        -D sonar.host.url=${SONNAR_URL} \
                        -D sonar.login=${SONNAR_TOKEN} \
                        -D sonar.projectName=TheTipTop'
                    }
                }
            }
            post{
                failure{
                    mail to: 'benbrahim.elmahdi@gmail.com',
                    subject: 'TheTipTop - SonarQube Failed',
                    body: 'TheTipTop - SonarQube Failed - ${BUILD_URL} - ${BUILD_NUMBER} - ${JOB_NAME} - ${GIT_COMMIT} - ${GIT_BRANCH}'
                }
            }
        }

        stage('Archive'){
            steps{
                archiveArtifacts artifacts: 'storage/logs/*.xml', fingerprint: true
            }
            post{
                failure{
                    mail to: 'benbrahim.elmahdi@gmail.com',
                    subject: 'TheTipTop - Archive Failed',
                    body: 'TheTipTop - Archive Failed - ${BUILD_URL} - ${BUILD_NUMBER} - ${JOB_NAME} - ${GIT_COMMIT} - ${GIT_BRANCH}'
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
                failure{
                    mail to: 'benbrahim.elmahdi@gmail.com',
                    subject: 'TheTipTop - Clean Failed',
                    body: 'TheTipTop - Clean Failed - ${BUILD_URL} - ${BUILD_NUMBER} - ${JOB_NAME} - ${GIT_COMMIT} - ${GIT_BRANCH}'
                }
            }
        }

        stage('Deploy Prod'){
            /* steps{
                script{
                    sshagent(['ssh-key']){
                        sh 'ssh -tt -o StrictHostKeyChecking=no -l root 64.226.113.4 "cd /var/www/ && docker kill thetiptop && docker rm thetiptop && docker pull ebenbrah/thetiptop:latest && docker run -d --name thetiptop ebenbrah/thetiptop"'
                    }
                }
            } */
            steps{
                script{
                    sshagent(['ssh-key']){
                        sh 'ssh -tt -o StrictHostKeyChecking=no -l root 64.226.113.4 "cd /var/www/ && docker kill thetiptop && docker rm thetiptop && docker pull ebenbrah/thetiptop:latest && docker run -d --name thetiptop ebenbrah/thetiptop"'
                    }
                }
            }
            post{
                failure{
                    mail to: 'benbrahim.elmahdi@gmail.com',
                    subject: 'TheTipTop - Deploy Prod Failed',
                    body: 'TheTipTop - Deploy Prod Failed - ${BUILD_URL} - ${BUILD_NUMBER} - ${JOB_NAME} - ${GIT_COMMIT} - ${GIT_BRANCH}'
                }
            }
        }
    }
}