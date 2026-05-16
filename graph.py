import numpy as np
import matplotlib.pyplot as plt
Q0=5*10**(-6)
omega = 100*np.pi
T=2*np.pi/omega
t = np.linspace(0, 2*T, 100)
Q = Q0*np.sin(omega*t)
plt.plot(t,Q)
plt.show()
plt.grid()