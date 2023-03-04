pipeline{
    agent any
    environment{
        imageName = 'thetiptop'
        registryCredential = 'dockerhubcreds'
        registry = 'docker.io'
        registryUrl = 'https://index.docker.io/v1/'
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
                    withSonarQubeEnv('sonarqube'){
                        ssh '${tool(SonarQube)}/bin/sonar-scanner \
                        -D sonar.projectKey=thetiptop \
                        -D sonar.source=. \
                        -D sonar.php.coverage.reportPaths=storage/logs/coverage.xml \
                        -D sonar.php.tests.reportPaths=storage/logs/phpunit.junit.xml'
                    }
                }
            }
        }

        // build docker image
        stage('Build'){
            steps{
                script{
                    docker.build(imageName)
                }

                echo 'Build'
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
                echo 'Push'
            }
        }

        // deploy docker image to preprod server with ssh
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

        stage('Merging'){
            steps{
                script{
                    sh 'git checkout master'
                    sh 'git merge develop'
                    sh 'git push origin master'
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

        // check status preprod server if it is running or not with ssh and notify by email if it is not running
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
}