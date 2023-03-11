pipeline{
    agent any
    environment{
        imageName = 'ebenbrah/thetiptop'
        localImageName = 'web'
        registryUsername= 'ebenbrah'
        registryCredential = 'dockerhubuser'
        registryCredentialToken = 'dockerhubtoken'
        registry = 'https://index.docker.io/v1/'
        SCANNER_HOME = tool 'sonar-scanner'
        nexusUrl = 'http://46.101.35.94:8082'
        nexusCredential = 'nexus'
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
        /* stage('Clean'){
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
        } */
        
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
                        sh '${SCANNER_HOME}/bin/sonar-scanner \
                        -D sonar.projectKey=TheTipTop \
                        -D sonar.sources=. \
                        -D sonar.php.coverage.reportPaths=storage/logs/coverage.xml \
                        -D sonar.php.tests.reportPaths=storage/logs/phpunit.junit.xml'
                    }
                }
            }
        }

        /* stage('Push'){
            steps{
                script{
                    withCredentials([usernamePassword(credentialsId: registryCredential, usernameVariable: 'DOCKERHUB_CREDS_USR', passwordVariable: 'DOCKERHUB_CREDS_PSW')]){
                        // sh 'echo $token | docker login -u $registryUsername --password-stdin $registry'
                        docker.withRegistry(registry, registryCredential){
                            docker.image(localImageName).inside{
                                sh 'docker build -t $localImageName .'
                                sh 'docker tag $localImageName:local $registryUsername/$localImageName:${env.BUILD_NUMBER}'
                                sh 'docker push $registryUsername/$localImageName'
                            }
                        }
                    }

                    withDockerRegistry([credentialsId: registryCredentialToken, url: registry]){
                        sh 'docker build -t $registryUsername/$localImageName:lastest .'
                        sh 'docker push $registryUsername/$localImageName'
                    }
                }
            }
        } */

        stage('Push to Nexus'){
            steps{
                script{
                    withCredentials([usernamePassword(credentialsId: nexusCredential, usernameVariable: 'NEXUS_CREDS_USR', passwordVariable: 'NEXUS_CREDS_PSW')]){
                        docker.withRegistry(nexusUrl, nexusCredential){
                            docker.image(localImageName).inside{
                                sh 'docker login -u $NEXUS_CREDS_USR -p $NEXUS_CREDS_PSW $nexusUrl'
                                sh 'docker build -t $localImageName .'
                                sh 'docker tag $localImageName:local $registryUsername/$imageName:${env.BUILD_NUMBER}'
                                sh 'docker push $registryUsername/$imageName'
                            }
                        }
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
}