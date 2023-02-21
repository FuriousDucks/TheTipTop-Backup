pipeline{
    agent any
    environment{
        imageName = 'thetiptop'
        registryCredential = 'dockerhub'
        registry = 'docker.io'
        registryUrl = 'https://index.docker.io/v1/'
    }
    options{
        buildDiscarder(logRotator(numToKeepStr: '5'))
    }
    stages{
        // checkout code from git
        stage('Checkout'){
            steps{
                deleteDir()
                checkout scm
            }
        }
        // Remove all containers and volumes
        stage('Clean'){
            steps{
                script{
                    sh 'which docker'
                    // sh "docker compose down -v"
                    // sh "docker system prune -af --volumes"
                }
            }
        }
        // start docker container with docker compose file
        stage('Start'){
            steps{
                echo 'Start docker container'
                /* script{
                    sh 'docker compose up -d'
                } */
            }
        }
        // run test cases phpunit
        /* stage('Test'){
            steps{
                script{
                    // install phpunit-bridge and browser-kit and css-selector
                    sh 'docker exec -it php composer require symfony/phpunit-bridge symfony/browser-kit symfony/css-selector --dev'
                    sh 'docker exec -it php php bin/phpunit --coverage-html=coverage --coverage-clover=coverage.xml'
                    // run test cases phpunit and report couverage and phpunit-report to store test result
                    sh 'docker exec -it php php bin/phpunit --coverage-clover storage/logs/coverage.xml --log-junit storage/logs/phpunit.junit.xml'
                    sh 'mkdir -p test-results'
                    // copy test result to test-results directory
                    sh 'docker cp php:/var/www/html/storage/logs/phpunit.junit.xml test-results'
                }
            }
            // publish test result
            post{
                always{
                    junit 'test-results/phpunit.junit.xml'
                }
            }
        } */
        
        // Analyze code with SonarQube with couverage and phpunit-report
        /* stage('SonarQube'){
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
        } */

        // build docker image
        /* stage('Build'){
            steps{
                script{
                    docker.build(imageName)
                }
            }
        } */

        // push docker image to docker hub
        /* stage('Push'){
            steps{
                script{
                    docker.withRegistry(registryUrl, registryCredential){
                        docker.image(imageName).push()
                    }
                }
            }
        } */

        // deploy docker image to preprod server with ssh
        /* stage('Deploy'){
            steps{
                script{
                    sshagent(['ssh-key']){
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker pull thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker stop thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker rm thetiptop"'
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker run -d -p 80:80 --name thetiptop thetiptop"'
                    }
                }
            }
        } */

        // check status preprod server if it is running or not with ssh and notify by email if it is not running
        /* stage('Check'){
            steps{
                script{
                    sshagent(['ssh-key']){
                        sh 'ssh -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_rsa root@preprod "docker ps"'
                    }
                }
            }
        } */
    }
}