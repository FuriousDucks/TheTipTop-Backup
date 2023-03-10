pipeline{
    agent any
    environment{
        imageName = 'ebenbrah/thetiptop'
        localImageName = 'web'
        registryUsername= 'ebenbrah'
        registryCredential = 'dockerhubuser'
        registryCredentialToken = 'dockerhubtoken'
        registry = 'https://index.docker.io/v1/'
        SONAR_HOST_URL = 'http://46.101.35.94:4000'
        SONAR_LOGIN = 'sqp_d0f92f1793bac86ed88d0990172c1db21aa35f8f'
        SCANNER_HOME = tool 'SonarQube'
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
                    sh 'docker compose down -v'
                    sh 'docker system prune -af --volumes'
                    // sh 'docker compose down'
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
                    sh 'docker exec -t web composer require --dev symfony/test-pack symfony/panther dbrekelmans/bdi --no-interaction --no-progress'
                    sh 'docker exec -t web vendor/bin/simple-phpunit --coverage-html=coverage --coverage-clover=coverage.xml'
                    sh 'docker exec -t web vendor/bin/simple-phpunit --coverage-clover storage/logs/coverage.xml --log-junit storage/logs/phpunit.junit.xml'
                    sh 'mkdir -p test-results'
                    sh 'docker cp web:/var/www/html/thetiptop/storage ${WORKSPACE}'
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
                        ssh '${SCANNER_HOME}/bin/sonar-scanner \
                        -D sonar.projectKey=TheTipTop \
                        -D sonar.sources=./thetiptop \
                        -D sonar.host.url=${env.SONAR_HOST_URL} \
                        -D sonar.login=${env.SONAR_LOGIN} \
                        -D sonar.php.coverage.reportPaths=storage/logs/coverage.xml \
                        -D sonar.php.tests.reportPaths=storage/logs/phpunit.junit.xml'
                    }
                }
            }
        }
    
        stage('Push'){
            steps{
                script{
                    withCredentials([string(credentialsId: registryCredentialToken, variable: 'token')]){
                        sh 'echo $token | docker login -u $registryUsername --password-stdin $registry'
                        sh 'docker push ${imageName}:${env.BUILD_NUMBER}'
                        sh 'docker push ${imageName}:latest'
                    }
                }
            }
        }

        stage('Deploy prod'){
            steps{
                /* script{
                    sshagent(['ssh-key']){
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@prod "docker pull thetiptop"'
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
    post{
        always{
            script{
                sh 'docker logout'
            }
        }
    }
}