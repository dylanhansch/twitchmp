#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
//#include <librtmp/rtmp.h>

void process_command(char *line);

int main(void){
	printf("Twitch Manager v1.0-SNAPSHOT\n");
	int c = 0;
	int position = 0;
#define line_max 256
	char line[line_max];
	while((c = getchar()) != EOF){
		if(c == '\n'){
			line[position] = '\0';
			process_command(line);
			position = 0;
		}else{
			line[position] = c;
			position += 1;
			if(position >= line_max)
				return 1;
		}
	}
}

// Act apon user input (commands)
void process_command(char *line){
	if(strcmp(line, "stop") == 0){ // Stop the daemon.
		printf("Exiting daemon.\n");
		exit(0);
	}else if(strcmp(line, "help") == 0){ // Display available commands.
		printf("Twitch Manager Help\n");
		printf("Format- CMD : Description\n\n");
		printf("help : Display available commands\n");
		printf("stop : Safely shutdown the daemon\n");
	}else{
		printf("Unknown command \"%s\".\n", line);
	}
}
