# goldphone
Always Online by Using GoldNet SIP Phone
(GoldPhone)
Voice Over IP (VoIP) is a well-known IP based service that mostly utilizes SIP for providing phone calls. Despite its popularity, sometimes it becomes useless. Actually the infrastructure must guarantee the application of this end-to-end protocol, but when it comes to cellphones Apple forces us to follow its own instructions. Of course it’s not that easy… 

From the iOS 10 and later on, your VoIP client is useless unless you be an Apple developer with good knowledge of network, or you pay for your calls to reach you!

Try GoldNet solution to receive your calls or to transfer your client calls to them. For end users, simply they configure their service provider settings on GoldPhone to use our Push Notification Service and miss no more calls. It’s completely compatible with all SIP services including call and messaging, we provide a SIP Push Notification framework for resolving iOS inbound calls/messages problem. 
In fact, The Apple Push server is the only one who can access iOS devices sending a Push notification to your phone, that alerts GoldPhone to wake-up and ring. This way, all problems with SIP calls on your SIP phone in Sleep/Lock mode are solved and you will be always reachable!

How to Setup:
In order to use our services in your SIP Server, please follow this instructions:
1.	First, you must set up authentication for your asterisk manager and change sth as below:
Uncomment enable option in /etc/asterisk/manager.conf 

Enable = yes

Add this to the end of file:

[admin]

secret = password

read = config,all

write = config,all


2.	Configure your dialplan:
You should call our sip push notification service in your dial plan (/etc/asterisk/extensions.conf) and manage your calls after using this service as below:
….
same => n,AGI(GoldPush.php)
same => n,Gotoif($["${SIPPush}" = "Yes"]?M:N) 

same => n(M),StartMusicOnHold(goldnet)
same => n,Wait(20)
same => n,Dial(SIP/${EXTEN})
same => n,Hangup()

same => n(N),Dial(SIP/${EXTEN},45,tr)
same => n,Hangup()

3.	Upload GoldNet SIP Push Notification service:
At the end, upload our PHPAGI in your sip server and the required files in the /var/lib/asterisk/agi-bin/ directory and configure required permission for them as below:

Chmod 777 /var/lib/asterisk/agi-bin/GoldPush.php

Remember that asterisk manager configuration (user and password set at part 1) must configure in GoldPush.php file.




