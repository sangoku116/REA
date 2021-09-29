#!/usr/bin/python3

import os
import sys
import atexit
import signal


def daemonize(pidfile, *, stdin='/dev/null',
                          stdout='/dev/null',
                          stderr='/dev/null'):
        if os.path.exists(pidfile):
                raise RuntimeError('Daemon Already Running!')

        forkId = os.fork()

        # First fork (detaches from parent)
        try:
                if forkId > 0:
                        raise SystemExit(0) # Parent exit, process gives up being the parent
        except OSError as e:
                raise RuntimeError('fork #1 failed.')

        os.chdir('/')
        os.umask(0)
        os.setsid()

        #Second fork (makes it a parent again)
        try:
                if forkId > 0:
                        raise SystemExit(0)
        except OSError as e:
                raise RuntimeError('fork 2 failed')

        #Flush I/O buffers
        sys.stdout.flush()
        sys.stderr.flush()

        #Replace file descriptors for stdin, stout, stderr
        with open(stdin, 'rb', 0) as f:
                os.dup2(f.fileno(),sys.stdin.fileno())
        with open(stdout, 'ab', 0) as f:
                os.dup2(f.fileno(),sys.stdout.fileno())
        with open(stderr, 'rb', 0) as f:
                os.dup2(f.fileno(),sys.stderr.fileno())

        #Write file for PID
        with open(pidfile, 'w') as f:
                print(os.getpid(), file=f)

        #Log into the PID file
        sys.stdout.write(f"Daemon started with PID: {os.getpid()}\n")

        #Arrange to have the PID file removed when daemon stopped
        atexit.register(lambda: os.remove(pidfile))

        #Signal handler for termination (required)
        def sigtermHandler(signo, frame):
                """
                handles sigterm
                :param signo: signal number
               :param frame:
                """
                sys.stdout.write(f"Exiting Daemon with PID: {os.getpid()}\n")
                killZombies()
                raise SystemExit(1)
        signal.signal(signal.SIGTERM, sigtermHandler)

def killZombies():
        """
        Kills zombie processes
        """
        try:
                processStatus = os.waitpid(0, 0)
                if processStatus == (0, 0):
                        pass
        except ChildProcessError:
                pass
        except OSError:
                sys.stderr.write("Error: Cannot kill zombie processes")

def main():
        """
         put daemon logs into a logfile
        """
        import time
        sys.stdout.write('Daemon started with pid {}\n'.format(os.getpid()))
        while True:
                sys.stdout.write('Daemon running! {}\n'.format(time.ctime()))
                time.sleep(10)

def stato():
        """
        starts/stop a service
        """
        PIDFILE = '/tmp/daemon.pid'

        if len(sys.argv) != 2:
                print('Usage: {} [start|stop]'.format(sys.argv[0]), file=sys.stderr)
                raise SystemExit(1)

        if sys.argv[1] == 'start':
                try:
                        daemonize(PIDFILE,
                                  stdout='/tmp/daemon.log',
                                  stderr='/tmp/daemon.log')

                except RuntimeError as e:
                        print(e, file=sys.stderr)
                        raise SystemExit(1)
                main()

        elif sys.argv[1] == 'stop':
                if os.path.exists(PIDFILE):
                        with open(PIDFILE) as f:
                                os.kill(int(f.read()), signal.SIGTERM)
                else:
                        print('Not running', file=sys.stderr)
                        raise SystemExit(1)
        else:
                print('Unknown command {!r}'.format(sys.argv[1]), file=sys.stderr)
                raise SystemExit(1)


if __name__ == '__main__':
    stato()
