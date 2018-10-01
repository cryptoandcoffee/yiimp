#Generates rc.local file that is correct for stratum algos
#!/bin/bash
rm rc.local_stratum
confs=( $(find ./stratum/config.sample/ -maxdepth 1 -printf '%P\n' | grep -v ".log" | grep -v ".sh" | sort) )
echo "STRATUM_DIR=/var/stratum" > rc.local_stratum

for conf in "${confs[@]}"; do
    suffix=".conf"
    algo=$(echo $conf | sed -e "s/$suffix$//" )
    echo "screen -dmS $algo \$STRATUM_DIR/run.sh $algo" >> rc.local_stratum
done
