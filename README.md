P A N D A
=========

A little PHP server offering a web interface and network API for playing back audio on the same machine as the server. This is useful for annoying people by allowing anyone on your internal network to hit the office speakers with some sweet text-to-speech or MP3 playback. Or even freshly recorded audio thanks to the Java widget. It is slightly more useful for broadcasting automated announcements, e.g., when deploying new code to your production website.

Requirements:
- PHP (some recentish version)
- Apache if you want to use the shortcut routes in `.htaccess`, or some other webserver
- MySQL for the MP3/audio file database
- `espeak` for text-to-speech playback
- `vlc` for audio file playback

The useful API routes:
- GET `play/[audiofile.mp3]`: play back the specified audio file (if it's been uploaded)
- POST `talk` params: `{txtmsg: "some message"}`: play text-to-speech audio of "some message"
