pipeline{
    agent any
    environment{
        imageName = 'web'
        localImageName = 'web'
        registryUsername= 'ebenbrah'
        registryCredential = 'dockerhubuser'
        registry = 'https://index.docker.io/v1/'
        SONAR_HOST_URL = 'http://46.101.35.94:4000'
        SONAR_LOGIN = 'sqp_fabaeb33f2ac71e0ad51dc9e525df34e982a6091'
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
                    // sh 'docker compose down -v'
                    // sh 'docker system prune -af --volumes'
                    sh 'docker compose down'
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
            steps{
                script{
                    withSonarQubeEnv('SonarQube'){
                        ssh '${tool("SCANNER_HOME")}/bin/sonar-scanner \
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
                    withCredentials([usernamePassword(credentialsId: registryCredential, passwordVariable: 'password', usernameVariable: 'username')]){
                        sh 'docker login -u $username -p $password $registry'
                        docker.image(imageName).push("$BUILD_NUMBER")
                        // docker.image(imageName).push("latest")
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