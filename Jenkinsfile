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

        stage('Clean Staging'){
            when{
                branch 'develop'
            }
            steps{
                script{
                    sh 'docker stop ${PREPROD_CONTAINER_NAME} && docker rm ${PREPROD_CONTAINER_NAME} || true'
                    sh 'docker rmi ${PREPROD_LOCAL_IMAGE} || true'
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

        stage('Clean Prod'){
            when{
                branch 'master'
            }
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
            when{
                branch 'develop'
            }
            steps{
                script{
                    sh 'docker compose -f docker-compose-preprod.yml up -d'
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
        
        stage('Test Staging'){
            when{
                branch 'develop'
            }
            steps{
                script{
                    sh 'docker exec -t ${PREPROD_CONTAINER_NAME} composer install -n'
                    sh 'docker exec -t ${PREPROD_CONTAINER_NAME} composer require --dev symfony/test-pack-n'
                    sh 'docker exec -t ${PREPROD_CONTAINER_NAME} vendor/bin/simple-phpunit --coverage-clover storage/logs/coverage.xml --log-junit storage/logs/phpunit.junit.xml'
                    sh 'mkdir -p storage'
                    sh 'docker cp ${PREPROD_CONTAINER_NAME}:/var/www/html/thetiptop/storage ${WORKSPACE}'
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
        
        stage('Push Staging'){
            when{
                branch 'develop'
            }
            steps{
                script{
                   docker.withRegistry('', registryCredential){
                        sh 'docker tag ${PREPROD_LOCAL_IMAGE} ${IMAGE_NAME}:$BUILD_NUMBER'
                        sh 'docker push ${PREPROD_IMAGE_NAME}:$BUILD_NUMBER'
                        sh 'docker tag ${PREPROD_LOCAL_IMAGE} ${IMAGE_NAME}:latest'
                        sh 'docker push ${PREPROD_IMAGE_NAME}:latest'
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

        stage('Push Prod'){
            when{
                branch 'master'
            }
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



        stage('Deploy Staging'){
            when{
                branch 'develop'
            }
            steps{
                script{
                    docker.withRegistry('', registryCredential){
                        sh 'docker pull ${PREPROD_IMAGE_NAME}:latest'
                        sh 'docker run -d --name ${PREPROD_CONTAINER_NAME} ${PREPROD_IMAGE_NAME}:latest'
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

        stage('Deploy Prod'){
            when{
                branch 'master'
            }
            steps{
                script{
                    docker.withRegistry('', registryCredential){
                        sh 'docker pull ${IMAGE_NAME}:latest'
                        sh 'docker run -d --name ${CONTAINER_NAME} ${IMAGE_NAME}:latest'
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