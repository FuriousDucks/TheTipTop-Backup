pipeline{
    agent any
    environment{
        imageName = 'ebenbrah/thetiptop'
        registryUsername= 'ebenbrah'
        registryCredential = 'dockerhubtoken'
        registry = 'https://index.docker.io/v1/'
    }
    /* options{
        buildDiscarder(logRotator(numToKeepStr: '5'))
    } */
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
        stage('Install dependencies'){
            steps{
                script{
                    sh 'docker exec -t web composer install --no-interaction --no-progress --no-suggest'
                }
            }
        }

        stage('Update database'){
            steps{
                script{
                    sh 'docker exec -t web php bin/console doctrine:database:create --if-not-exists'
                    sh 'docker exec -t web php bin/console doctrine:migrations:migrate --no-interaction'
                }
            }
        }

        stage('Test'){
            steps{
                script{
                    sh 'docker exec -t web composer require --dev symfony/test-pack symfony/panther --no-interaction --no-progress --no-suggest'
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
            environment{
                SONAR_HOST_URL = 'http://46.101.35.94:4000'
                SONAR_LOGIN = 'sqp_fabaeb33f2ac71e0ad51dc9e525df34e982a6091'
                SCANNER_HOME = tool 'SonarQube'
            }
            steps{
                script{
                    withSonarQubeEnv('SonarQube'){
                        ssh '${SCANNER_HOME}/bin/sonar-scanner \
                        -Dsonar.projectKey=TheTipTop \
                        -Dsonar.sources=. \
                        -Dsonar.host.url=${SONAR_HOST_URL} \
                        -Dsonar.login=${SONAR_LOGIN} \
                        -Dsonar.php.coverage.reportPaths=storage/logs/coverage.xml \
                        -Dsonar.php.tests.reportPaths=storage/logs/phpunit.junit.xml'
                    }
                }
            }
        }

        /* stage('Build'){
            steps{
                script{
                    docker.build("${imageName}:latest")
                }
            }
        } */

        stage('Push'){
            steps{
                script{
                    withCredentials([string(credentialsId: registryCredential, variable: 'DOCKERHUB_TOKEN')]) {
                        sh 'echo $DOCKERHUB_TOKEN | docker login --username ${registryUsername} --password-stdin'
                        sh 'docker push ${imageName}:${env.BUILD_NUMBER}'
                        sh 'docker push ${imageName}:latest'
                    }
                }
            }
        }

        stage('Deploy preprod'){
            steps{
                /* script{
                    sshagent(['ssh-key']){
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker pull thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker stop thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker rm thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker run -d -p 80:80 --name thetiptop thetiptop"'
                    }
                } */
                echo 'Deploy preprod'
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
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker ps"'
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