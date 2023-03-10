pipeline{
    agent any
    environment{
        imageName = 'ebenbrah/thetiptop'
        localImageName = 'web'
        registryUsername= 'ebenbrah'
        registryCredential = 'dockerhubuser'
        registryCredentialToken = 'dockerhubtoken'
        registry = 'https://index.docker.io/v1/'
        SONAR_HOST_URL = 'http://46.101.35.94:9091'
        SONAR_LOGIN = 'sqp_0effeac12cdcd4df6c3d7411f4aa319396dafcc9'
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
                        ssh 'sonar-scanner \
                        -Dsonar.projectKey=TheTipTop \
                        -Dsonar.sources=. \
                        -Dsonar.host.url=${env.SONAR_HOST_URL} \
                        -Dsonar.login=${env.SONAR_LOGIN}'
                    }
                }
            }
        }

        stage('Tag') {
            steps {
                sh 'docker tag ${imageName} ${imageName}:$BUILD_NUMBER'
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