def composeFiles = ['master' : 'docker-compose.yml', 'develop' : 'docker-compose-preprod.yml']
def imageNames = ['master' : 'ebenbrah/thetiptop', 'develop' : 'ebenbrah/preprodthetiptop']
def localImageNames = ['master' : 'thetiptop', 'develop' : 'preprod_thetiptop']
def containerNames = ['master' : 'web_thetiptop', 'develop' : 'preprod_web_thetiptop']
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
                when{
                   anyOf{
                       branch 'master'
                       branch 'develop'
                   }
                }
                script{
                    def imageName = imageNames[env.BRANCH_NAME]
                    def containerName = containerNames[env.BRANCH_NAME]
                    sh 'docker stop ${containerName} && docker rm ${containerName} || true'
                    sh 'docker rmi ${imageName} || true'
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
                when{
                   anyOf{
                       branch 'master'
                       branch 'develop'
                   }
                }
                script{
                    def composeFile = composeFiles[env.BRANCH_NAME]
                    sh 'docker compose -f ${composeFile} up -d'
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
                when{
                   anyOf{
                       branch 'master'
                       branch 'develop'
                   }
                }
                script{
                    def containerName = containerNames[env.BRANCH_NAME]
                    sh 'docker exec -t ${containerName} composer install -n'
                    sh 'docker exec -t ${containerName} composer require --dev symfony/test-pack symfony/browser-kit symfony/css-selector -n'
                    sh 'docker exec -t ${containerName} vendor/bin/simple-phpunit --coverage-clover storage/logs/coverage.xml --log-junit storage/logs/phpunit.junit.xml'
                    sh 'mkdir -p storage'
                    sh 'docker cp ${containerName}:/var/www/html/thetiptop/storage ${WORKSPACE}'
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
                when{
                   anyOf{
                       branch 'master'
                       branch 'develop'
                   }
                }
                script{
                    def imageName = imageNames[env.BRANCH_NAME]
                    def localImageName = localImageNames[env.BRANCH_NAME]
                   docker.withRegistry('', registryCredential){
                        sh 'docker tag ${localImageName} ${imageName}:$BUILD_NUMBER'
                        sh 'docker push ${imageName}:$BUILD_NUMBER'
                        sh 'docker tag ${localImageName} ${imageName}:latest'
                        sh 'docker push ${imageName}:latest'
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
            steps{
                script{
                    docker.withRegistry('', registryCredential){
                        def imageName = imageNames[env.BRANCH_NAME]
                        def containerName = containerNames[env.BRANCH_NAME]
                        sh 'docker pull ${imageName}:latest'
                        sh 'docker stop ${containerName} && docker rm ${containerName} || true'
                        sh 'docker run -d --name ${containerName} ${imageName}:latest'
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