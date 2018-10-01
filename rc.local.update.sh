#Generates an rc.local file that is correct for stratum algos
#!/bin/bash
confs=( $(find ./stratum/config.sample/ -maxdepth 1 -printf '%P\n' | grep -v ".log" | grep -v ".sh" | sort) )

for conf in "${confs[@]}"; do
    suffix=".conf"
    algo=$(echo $conf | sed -e "s/$suffix$//" )
    echo "screen -dmS $algo $STRATUM_DIR/run.sh $algo"
done