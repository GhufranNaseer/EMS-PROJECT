FROM ubuntu:latest

# Install cron and any other dependencies
RUN apt-get update && apt-get install -y \
    cron \
    wget \
    && rm -rf /var/lib/apt/lists/*

# Copy your cron script(s) into the container
# COPY ./cron/cron_scripts /cron_scripts

# Add crontab file in the cron directory
ADD ./cron/crontab /etc/cron.d/my_cronjob

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/my_cronjob

# Apply cron job
RUN crontab /etc/cron.d/my_cronjob

# Run the command on container startup
CMD ["cron", "-f"]
