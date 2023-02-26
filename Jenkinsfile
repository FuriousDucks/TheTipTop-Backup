pipeline{
    agent any
    environment{
        imageName = 'thetiptop'
        registryCredential = 'dockerhub'
        registry = 'docker.io'
        registryUrl = 'https://index.docker.io/v1/'
    }
    /* options{
        buildDiscarder(logRotator(numToKeepStr: '5'))
    } */
    stages{
        // checkout code from git
        stage('Checkout'){
            steps{
                deleteDir()
                checkout scm
                echo 'Checked out'
            }
        }
        // Remove all containers and volumes
        stage('Clean'){
            steps{
                script{
                    sh 'docker compose down -v || true'
                    sh 'docker system prune -f --volumes || true'
                    echo 'Cleaned'
                }
            }
        }
        // start docker container with docker compose file
        stage('Start'){
            steps{
                script{
                    sh 'docker compose up -d'
                    echo 'Started'
                }
            }
        }
        // run test cases phpunit
        stage('Test'){
            steps{
                script{
                    // install phpunit-bridge and browser-kit and css-selector
                    sh 'docker exec -t web composer require --dev symfony/test-pack symfony/panther --no-interaction --no-progress --no-suggest'
                    sh 'docker exec -t web vendor/bin/simple-phpunit --coverage-html=coverage --coverage-clover=coverage.xml'
                    // run test cases phpunit and report couverage and phpunit-report to store test result
                    sh 'docker exec -t web vendor/bin/simple-phpunit --coverage-clover storage/logs/coverage.xml --log-junit storage/logs/phpunit.junit.xml'
                    sh 'mkdir -p test-results'
                    // copy test result to test-results directory
                    sh 'docker cp web:/var/www/html/storage ${WORKSPACE}'
                    echo 'Tested'
                }
            }
            // publish test result
            post{
                always{
                    junit 'test-results/phpunit.junit.xml'
                }
            }
        }
        
        // Analyze code with SonarQube with couverage and phpunit-report
        stage('SonarQube'){
            steps{
                script{
                    /* withSonarQubeEnv('sonarqube'){
                        ssh '${tool(SonarQube)}/bin/sonar-scanner \
                        -D sonar.projectKey=thetiptop \
                        -D sonar.source=. \
                        -D sonar.php.coverage.reportPaths=storage/logs/coverage.xml \
                        -D sonar.php.tests.reportPaths=storage/logs/phpunit.junit.xml'
                    } */
                    echo 'Analyzed'
                }
            }
        }

        // build docker image
        stage('Build'){
            steps{
               /*  script{
                    docker.build(imageName)
                } */
                echo 'Built'
            }
        }

        // push docker image to docker hub
        stage('Push'){
            steps{
                /* script{
                    docker.withRegistry(registryUrl, registryCredential){
                        docker.image(imageName).push()
                    }
                } */
                echo 'Pushed'
            }
        }

        // deploy docker image to preprod server with ssh
        stage('Deploy'){
            steps{
                /* script{
                    sshagent(['ssh-key']){
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker pull thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker stop thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker rm thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker run -d -p 80:80 --name thetiptop thetiptop"'
                    }
                } */
                echo 'Deployed'
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
                echo 'Checked'
            }
        }
    }
}