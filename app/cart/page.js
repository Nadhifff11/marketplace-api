"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { apiFetch } from "@/lib/api";

export default function CartPage() {
  const router = useRouter();
  const [cart, setCart] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const loadCart = () => {
    setLoading(true);
    apiFetch("/cart")
      .then((data) => setCart(data))
      .catch((err) => {
        if (err.message.includes("Unauthenticated")) {
          router.push("/login");
        } else {
          setError(err.message);
        }
      })
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) {
      router.push("/login");
      return;
    }
    loadCart();
  }, []);

  const handleRemove = async (itemId) => {
    try {
      await apiFetch(`/cart/${itemId}`, { method: "DELETE" });
      loadCart();
    } catch (err) {
      alert("Gagal menghapus: " + err.message);
    }
  };

  const handleQuantityChange = async (itemId, quantity) => {
    if (quantity < 1) return;
    try {
      await apiFetch(`/cart/${itemId}`, {
        method: "PUT",
        body: JSON.stringify({ quantity }),
      });
      loadCart();
    } catch (err) {
      alert("Gagal update: " + err.message);
    }
  };

  if (loading) return <p style={{ padding: 20 }}>Loading...</p>;
  if (error) return <p style={{ padding: 20, color: "red" }}>{error}</p>;

  const items = cart?.items || [];
  const total = items.reduce(
    (sum, item) => sum + item.product.price * item.quantity,
    0
  );

  return (
    <div style={{ padding: 20 }}>
      <Link href="/">&larr; Kembali Belanja</Link>
      <h1>Keranjang Saya</h1>

      {items.length === 0 ? (
        <p>Keranjang kosong.</p>
      ) : (
        <>
          {items.map((item) => (
            <div
              key={item.id}
              style={{
                border: "1px solid #ccc",
                borderRadius: 8,
                padding: 12,
                marginBottom: 10,
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
              }}
            >
              <div>
                <h3>{item.product.name}</h3>
                <p>Rp {Number(item.product.price).toLocaleString("id-ID")}</p>
              </div>
              <div>
                <button
                  onClick={() =>
                    handleQuantityChange(item.id, item.quantity - 1)
                  }
                >
                  -
                </button>
                <span style={{ margin: "0 10px" }}>{item.quantity}</span>
                <button
                  onClick={() =>
                    handleQuantityChange(item.id, item.quantity + 1)
                  }
                >
                  +
                </button>
              </div>
              <button onClick={() => handleRemove(item.id)}>Hapus</button>
            </div>
          ))}

          <h2>Total: Rp {total.toLocaleString("id-ID")}</h2>
          <Link href="/checkout">
            <button>Lanjut ke Checkout</button>
          </Link>
        </>
      )}
    </div>
  );
}